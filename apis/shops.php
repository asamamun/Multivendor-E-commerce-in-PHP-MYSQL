<?php
/**
 * GET /apis/shops.php
 * Returns paginated list of active vendor shops
 * Query params: page, limit, search
 *
 * GET /apis/shops.php?id=VENDOR_ID
 * Returns single shop with its products
 */
require __DIR__ . '/helpers.php';
require __DIR__ . '/../db/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respond_error('Method not allowed', 405);
}

// Single shop detail
if (isset($_GET['id'])) {
    $vendor_id = (int)$_GET['id'];

    $stmt = $conn->prepare(
        "SELECT u.id AS vendor_id, u.name AS vendor_name, u.email, u.phone, u.status
         FROM users u
         WHERE u.id = ? AND u.role = 'vendor'"
    );
    $stmt->bind_param('i', $vendor_id);
    $stmt->execute();
    $shop = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$shop) {
        respond_error('Shop not found', 404);
    }

    // Fetch shop products (first page, 12 items)
    $p_stmt = $conn->prepare(
        "SELECT p.id, p.name, p.slug, p.price, p.compare_price,
                p.stock_quantity, p.rating, p.review_count, p.featured,
                (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC LIMIT 1) AS primary_image
         FROM products p
         WHERE p.vendor_id = ? AND p.status = 'active' AND p.deleted_at IS NULL
         ORDER BY p.created_at DESC
         LIMIT 12"
    );
    $p_stmt->bind_param('i', $vendor_id);
    $p_stmt->execute();
    $shop['products'] = $p_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $p_stmt->close();

    respond_success(['shop' => $shop]);
}

// Shop list
$page   = max(1, (int)($_GET['page']  ?? 1));
$limit  = min(50, max(1, (int)($_GET['limit'] ?? 12)));
$offset = ($page - 1) * $limit;
$search = trim($_GET['search'] ?? '');

$conditions = ["u.role = 'vendor'", "u.status = 'active'"];
$params = [];
$types  = '';

if ($search !== '') {
    $conditions[] = "u.name LIKE ?";
    $like = "%$search%";
    $params[] = $like;
    $types   .= 's';
}

$where = 'WHERE ' . implode(' AND ', $conditions);

// Count
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users u $where");
if ($types) $stmt->bind_param($types, ...$params);
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fetch
$sql = "SELECT u.id AS vendor_id, u.name AS vendor_name, u.email, u.phone,
               (SELECT COUNT(*) FROM products p WHERE p.vendor_id = u.id AND p.status = 'active' AND p.deleted_at IS NULL) AS product_count,
               (SELECT image_path FROM product_images pi2 
                JOIN products p2 ON pi2.product_id = p2.id 
                WHERE p2.vendor_id = u.id AND pi2.is_primary = 1 LIMIT 1) AS sample_image
        FROM users u
        $where
        ORDER BY u.created_at DESC
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types   .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$shops = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

respond_success([
    'shops'      => $shops,
    'pagination' => [
        'page'        => $page,
        'limit'       => $limit,
        'total'       => (int)$total,
        'total_pages' => (int)ceil($total / $limit),
    ]
]);
