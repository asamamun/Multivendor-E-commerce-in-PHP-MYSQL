<?php
/**
 * GET /apis/products.php
 * Query params:
 *   page     (default 1)
 *   limit    (default 12, max 50)
 *   category (category id, optional)
 *   search   (keyword, optional)
 *   vendor   (vendor id, optional)
 *   featured (1 = featured only, optional)
 */
require __DIR__ . '/helpers.php';
require __DIR__ . '/../db/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    respond_error('Method not allowed', 405);
}

$page     = max(1, (int)($_GET['page']  ?? 1));
$limit    = min(50, max(1, (int)($_GET['limit'] ?? 12)));
$offset   = ($page - 1) * $limit;
$category = (int)($_GET['category'] ?? 0);
$vendor   = (int)($_GET['vendor']   ?? 0);
$search   = trim($_GET['search']    ?? '');
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;

// Build WHERE conditions
$conditions = ["p.status = 'active'", "p.deleted_at IS NULL"];
$params     = [];
$types      = '';

if ($category > 0) {
    $conditions[] = "(p.category_id = ? OR p.category_id IN (SELECT id FROM categories WHERE parent_id = ?))";
    $params[] = $category;
    $params[] = $category;
    $types   .= 'ii';
}

if ($vendor > 0) {
    $conditions[] = "p.vendor_id = ?";
    $params[] = $vendor;
    $types   .= 'i';
}

if ($search !== '') {
    $conditions[] = "(p.name LIKE ? OR p.short_description LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}

if ($featured === 1) {
    $conditions[] = "p.featured = 1";
}

$where = 'WHERE ' . implode(' AND ', $conditions);

// Total count
$count_sql = "SELECT COUNT(*) as total FROM products p $where";
$stmt = $conn->prepare($count_sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Fetch products
$sql = "SELECT p.id, p.name, p.slug, p.price, p.compare_price, p.stock_quantity,
               p.short_description, p.featured, p.rating, p.review_count,
               c.name AS category_name,
               u.name AS vendor_name,
               (SELECT image_path FROM product_images WHERE product_id = p.id ORDER BY is_primary DESC, sort_order ASC LIMIT 1) AS primary_image
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN users u      ON p.vendor_id   = u.id
        $where
        ORDER BY p.created_at DESC
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$types   .= 'ii';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

respond_success([
    'products'   => $rows,
    'pagination' => [
        'page'        => $page,
        'limit'       => $limit,
        'total'       => (int)$total,
        'total_pages' => (int)ceil($total / $limit),
    ]
]);
