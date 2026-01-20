<?php
session_start();
include_once "db/db.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Order ID");
}

if (!isset($_SESSION['user_id'])) {
    die("Please login to view invoice");
}

$order_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'] ?? 'customer';

// Fetch Order
$sql = "SELECT o.*, u.name as customer_name, u.email as customer_email, u.phone as customer_phone 
        FROM orders o 
        JOIN users u ON o.customer_id = u.id 
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_result = $stmt->get_result();

if ($order_result->num_rows === 0) {
    die("Order not found");
}

$order = $order_result->fetch_assoc();

// Access Control
$can_view = false;
if ($user_role === 'admin') {
    $can_view = true;
} elseif ($user_role === 'customer' && $order['customer_id'] == $user_id) {
    $can_view = true;
} elseif ($user_role === 'vendor') {
    // Check if vendor has items in this order
    $v_sql = "SELECT 1 FROM order_items WHERE order_id = ? AND vendor_id = ?";
    $v_stmt = $conn->prepare($v_sql);
    $v_stmt->bind_param("ii", $order_id, $user_id);
    $v_stmt->execute();
    if ($v_stmt->get_result()->num_rows > 0) {
        $can_view = true;
    }
}

if (!$can_view) {
    die("Unauthorized access to this invoice");
}

// Fetch Order Items
// Join users table to get vendor name for Admin view
$items_sql = "SELECT oi.*, p.sku, u.name as vendor_name 
              FROM order_items oi 
              LEFT JOIN products p ON oi.product_id = p.id
              LEFT JOIN users u ON oi.vendor_id = u.id
              WHERE oi.order_id = ?";

// If vendor, only show their items
if ($user_role === 'vendor') {
    $items_sql .= " AND oi.vendor_id = ?";
    $stmt = $conn->prepare($items_sql);
    $stmt->bind_param("ii", $order_id, $user_id);
} else {
    $stmt = $conn->prepare($items_sql);
    $stmt->bind_param("i", $order_id);
}

$stmt->execute();
$items_result = $stmt->get_result();
$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}

// Update payment info fetch (if needed for display)
$pay_sql = "SELECT * FROM payments WHERE order_id = ?";
$stmt = $conn->prepare($pay_sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();

$shipping = json_decode($order['shipping_address'], true);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - #<?php echo $order['order_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f5f5f5; }
        .invoice-container { background: white; padding: 40px; margin: 30px auto; max-width: 850px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        @media print {
            body { background: white; }
            .invoice-container { box-shadow: none; margin: 0; max-width: 100%; padding: 20px; }
            .no-print { display: none !important; }
        }
        .invoice-header { border-bottom: 2px solid #eee; margin-bottom: 20px; padding-bottom: 20px; }
        .invoice-footer { border-top: 2px solid #eee; margin-top: 30px; padding-top: 20px; font-size: 0.9em; text-align: center; color: #777; }
    </style>
</head>
<body>

<div class="container">
    <div class="invoice-container">
        
        <!-- Actions -->
        <div class="d-flex justify-content-between mb-4 no-print">
            <a href="index.php" class="btn btn-outline-secondary"><i class="fas fa-arrow-left"></i> Back to Home</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Invoice</button>
        </div>

        <!-- Header -->
        <div class="row invoice-header">
            <div class="col-6">
                <h2 class="text-primary fw-bold">INVOICE</h2>
                <p class="mb-0">Order: <strong>#<?php echo htmlspecialchars($order['order_number']); ?></strong></p>
                <p class="mb-0">Date: <?php echo date('d M, Y h:i A', strtotime($order['created_at'])); ?></p>
                <p class="mb-0">Status: 
                    <span class="badge bg-<?php echo $order['order_status'] == 'completed' ? 'success' : 'warning'; ?>">
                        <?php echo ucfirst($order['order_status']); ?>
                    </span>
                </p>
                <p class="mb-0">Payment: 
                    <span class="badge bg-<?php echo $order['payment_status'] == 'paid' ? 'success' : 'secondary'; ?>">
                        <?php echo ucfirst($order['payment_status']); ?>
                    </span>
                    (<?php echo ucfirst($order['payment_method']); ?>)
                    <?php if ($user_role === 'admin' && $order['payment_method'] !== 'cod' && !empty($payment['transaction_id'])): ?>
                        <br>
                        <small class="text-muted">TrxID: <strong><?php echo htmlspecialchars($payment['transaction_id']); ?></strong></small>
                    <?php endif; ?>
                </p>
            </div>
            <div class="col-6 text-end">
                <h3>MarketPlace</h3>
                <p class="mb-0">123 E-commerce St, Digital City</p>
                <p class="mb-0">support@marketplace.com</p>
                <p class="mb-0">+880 1234 567890</p>
            </div>
        </div>

        <!-- Addresses -->
        <div class="row mb-4">
            <div class="col-6">
                <h5 class="text-muted">Bill To:</h5>
                <p class="fw-bold mb-1"><?php echo htmlspecialchars($shipping['name'] ?? $order['customer_name']); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($shipping['address'] ?? ''); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($shipping['city'] ?? '') . ', ' . htmlspecialchars($shipping['zip'] ?? ''); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($shipping['phone'] ?? $order['customer_phone']); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($order['customer_email']); ?></p>
            </div>
            <div class="col-6 text-end">
                <!-- Can put Ship To here if different, for now same -->
                <h5 class="text-muted">Ship To:</h5>
                <p class="fw-bold mb-1"><?php echo htmlspecialchars($shipping['name'] ?? $order['customer_name']); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($shipping['address'] ?? ''); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($shipping['city'] ?? '') . ', ' . htmlspecialchars($shipping['zip'] ?? ''); ?></p>
                <p class="mb-0"><?php echo htmlspecialchars($shipping['phone'] ?? $order['customer_phone']); ?></p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <?php if ($user_role === 'admin'): ?>
                        <th>Vendor</th>
                    <?php endif; ?>
                    <th class="text-end">Unit Price</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td>
                        <?php echo htmlspecialchars($item['product_name']); ?>
                        <?php if ($item['product_sku']): ?>
                            <br><small class="text-muted">SKU: <?php echo htmlspecialchars($item['product_sku']); ?></small>
                        <?php endif; ?>
                    </td>
                    <?php if ($user_role === 'admin'): ?>
                        <td>
                            <?php echo htmlspecialchars($item['vendor_name']); ?>
                            <br><small class="text-muted">ID: <?php echo $item['vendor_id']; ?></small>
                        </td>
                    <?php endif; ?>
                    <td class="text-end">৳<?php echo number_format($item['unit_price'], 2); ?></td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-end">৳<?php echo number_format($item['total_price'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <?php $colspan = ($user_role === 'admin') ? 5 : 4; ?>
                    <td colspan="<?php echo $colspan; ?>" class="text-end border-0">Subtotal</td>
                    <td class="text-end border-0">৳<?php echo number_format($order['subtotal'], 2); ?></td>
                </tr>
                <tr>
                    <?php $colspan = ($user_role === 'admin') ? 5 : 4; ?>
                    <td colspan="<?php echo $colspan; ?>" class="text-end border-0">Shipping Cost</td>
                    <td class="text-end border-0">৳<?php echo number_format($order['shipping_cost'], 2); ?></td>
                </tr>
                <tr>
                    <?php if ($user_role === 'admin'): ?>
                        <td colspan="5" class="text-end fw-bold fs-5">Total</td>
                    <?php else: ?>
                        <td colspan="4" class="text-end fw-bold fs-5">Total</td>
                    <?php endif; ?>
                    <td class="text-end fw-bold fs-5">৳<?php echo number_format($order['total_amount'], 2); ?></td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="invoice-footer">
            <p>Thank you for shopping with us!</p>
            <p>For any queries, please contact support@marketplace.com quoting Order #<?php echo $order['order_number']; ?></p>
        </div>

    </div>
</div>

</body>
</html>
