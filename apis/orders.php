<?php
/**
 * GET  /apis/orders.php          — customer's order history (auth required)
 * GET  /apis/orders.php?id=X     — single order detail (auth required)
 * POST /apis/orders.php          — place a new order (auth required)
 *
 * POST body for placing order:
 * {
 *   "items": [{ "product_id": 1, "quantity": 2 }, ...],
 *   "shipping_address": { "name":"", "phone":"", "address":"", "city":"", "zip":"" },
 *   "payment_method": "bkash|nagad|cod|rocket",
 *   "notes": ""
 * }
 */
require __DIR__ . '/helpers.php';
require __DIR__ . '/../db/db.php';

$auth = require_auth();
$customer_id = (int)$auth['user_id'];

// ── GET: order list or single order ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    if (isset($_GET['id'])) {
        $order_id = (int)$_GET['id'];

        $stmt = $conn->prepare(
            "SELECT o.*, d.status AS delivery_status, d.tracking_number
             FROM orders o
             LEFT JOIN deliveries d ON d.order_id = o.id
             WHERE o.id = ? AND o.customer_id = ?"
        );
        $stmt->bind_param('ii', $order_id, $customer_id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$order) respond_error('Order not found', 404);

        // Decode JSON address fields
        $order['shipping_address'] = json_decode($order['shipping_address'], true);
        $order['billing_address']  = json_decode($order['billing_address'],  true);

        // Order items
        $stmt = $conn->prepare(
            "SELECT oi.*, 
                    (SELECT image_path FROM product_images WHERE product_id = oi.product_id ORDER BY is_primary DESC LIMIT 1) AS product_image
             FROM order_items oi
             WHERE oi.order_id = ?"
        );
        $stmt->bind_param('i', $order_id);
        $stmt->execute();
        $order['items'] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        respond_success(['order' => $order]);
    }

    // Order list with pagination
    $page   = max(1, (int)($_GET['page']  ?? 1));
    $limit  = min(50, max(1, (int)($_GET['limit'] ?? 10)));
    $offset = ($page - 1) * $limit;

    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM orders WHERE customer_id = ?");
    $stmt->bind_param('i', $customer_id);
    $stmt->execute();
    $total = $stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();

    $stmt = $conn->prepare(
        "SELECT o.id, o.order_number, o.total_amount, o.currency,
                o.payment_method, o.payment_status, o.order_status,
                o.shipping_address, o.created_at,
                COUNT(oi.id) AS item_count
         FROM orders o
         LEFT JOIN order_items oi ON oi.order_id = o.id
         WHERE o.customer_id = ?
         GROUP BY o.id
         ORDER BY o.created_at DESC
         LIMIT ? OFFSET ?"
    );
    $stmt->bind_param('iii', $customer_id, $limit, $offset);
    $stmt->execute();
    $orders = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Decode shipping address for each order
    foreach ($orders as &$o) {
        $o['shipping_address'] = json_decode($o['shipping_address'], true);
    }

    respond_success([
        'orders'     => $orders,
        'pagination' => [
            'page'        => $page,
            'limit'       => $limit,
            'total'       => (int)$total,
            'total_pages' => (int)ceil($total / $limit),
        ]
    ]);
}

// ── POST: place order ─────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $body            = get_json_body();
    $items           = $body['items']            ?? [];
    $shipping_addr   = $body['shipping_address'] ?? [];
    $payment_method  = $body['payment_method']   ?? '';
    $notes           = $body['notes']            ?? '';

    // Validate
    if (empty($items)) {
        respond_error('Order must contain at least one item');
    }
    if (empty($shipping_addr['name']) || empty($shipping_addr['address']) || empty($shipping_addr['phone'])) {
        respond_error('shipping_address must include name, phone and address');
    }
    if (!in_array($payment_method, ['bkash', 'nagad', 'cod', 'rocket'])) {
        respond_error('Invalid payment_method. Use: bkash, nagad, cod, rocket');
    }

    // Validate products and calculate totals
    $order_items  = [];
    $subtotal     = 0.0;
    $shipping_cost = 60.0;

    foreach ($items as $item) {
        $product_id = (int)($item['product_id'] ?? 0);
        $quantity   = max(1, (int)($item['quantity'] ?? 1));

        if (!$product_id) continue;

        $stmt = $conn->prepare(
            "SELECT id, name, sku, price, vendor_id, stock_quantity FROM products WHERE id = ? AND status = 'active' AND deleted_at IS NULL"
        );
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $product = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$product) {
            respond_error("Product ID $product_id not found or unavailable");
        }
        if ($product['stock_quantity'] < $quantity) {
            respond_error("Insufficient stock for: {$product['name']}");
        }

        $line_total    = $product['price'] * $quantity;
        $subtotal     += $line_total;
        $order_items[] = [
            'product_id'   => $product_id,
            'vendor_id'    => $product['vendor_id'],
            'product_name' => $product['name'],
            'product_sku'  => $product['sku'],
            'quantity'     => $quantity,
            'unit_price'   => $product['price'],
            'total_price'  => $line_total,
        ];
    }

    if (empty($order_items)) {
        respond_error('No valid items in order');
    }

    $total_amount    = $subtotal + $shipping_cost;
    $order_number    = 'ORD-' . strtoupper(uniqid());
    $shipping_json   = json_encode($shipping_addr);

    // Begin transaction
    $conn->begin_transaction();
    try {
        // Insert order
        $ins = $conn->prepare(
            "INSERT INTO orders (order_number, customer_id, subtotal, shipping_cost, total_amount, payment_method, shipping_address, billing_address, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $ins->bind_param('sidddssss',
            $order_number, $customer_id, $subtotal, $shipping_cost,
            $total_amount, $payment_method, $shipping_json, $shipping_json, $notes
        );
        $ins->execute();
        $order_id = $ins->insert_id;
        $ins->close();

        // Insert order items & decrement stock
        foreach ($order_items as $oi) {
            $stmt = $conn->prepare(
                "INSERT INTO order_items (order_id, product_id, vendor_id, product_name, product_sku, quantity, unit_price, total_price)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('iiissidd',
                $order_id, $oi['product_id'], $oi['vendor_id'],
                $oi['product_name'], $oi['product_sku'],
                $oi['quantity'], $oi['unit_price'], $oi['total_price']
            );
            $stmt->execute();
            $stmt->close();

            // Decrement stock
            $conn->query("UPDATE products SET stock_quantity = stock_quantity - {$oi['quantity']} WHERE id = {$oi['product_id']}");
        }

        // Insert payment record
        $stmt = $conn->prepare("INSERT INTO payments (order_id, payment_method, amount) VALUES (?, ?, ?)");
        $stmt->bind_param('isd', $order_id, $payment_method, $total_amount);
        $stmt->execute();
        $stmt->close();

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        respond_error('Failed to place order: ' . $e->getMessage(), 500);
    }

    respond_success([
        'order_id'     => $order_id,
        'order_number' => $order_number,
        'total_amount' => $total_amount,
        'item_count'   => count($order_items),
    ], 'Order placed successfully');
}

respond_error('Method not allowed', 405);
