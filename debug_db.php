<?php
include "db/db.php";

echo "<h2>Database Debugger</h2>";

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connected successfully.<br>";

// 1. Check if 'featured', 'status', 'deleted_at' columns exist
$columns = [];
$res = $conn->query("SHOW COLUMNS FROM products");
if ($res) {
    echo "<h3>Products Table Columns:</h3><ul>";
    while ($row = $res->fetch_assoc()) {
        $columns[] = $row['Field'];
        echo "<li>" . $row['Field'] . " (" . $row['Type'] . ")</li>";
    }
    echo "</ul>";
} else {
    echo "Error showing columns: " . $conn->error . "<br>";
}

// 2. Count products by Status
$sql = "SELECT status, COUNT(*) as count FROM products GROUP BY status";
$res = $conn->query($sql);
echo "<h3>Products by Status:</h3>";
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo "Status: " . $row['status'] . " - Count: " . $row['count'] . "<br>";
    }
} else {
    echo "Error: " . $conn->error . "<br>";
}

// 3. Count products by Featured
$sql = "SELECT featured, COUNT(*) as count FROM products GROUP BY featured";
$res = $conn->query($sql);
echo "<h3>Products by Featured:</h3>";
if ($res) {
    while ($row = $res->fetch_assoc()) {
        echo "Featured: " . $row['featured'] . " - Count: " . $row['count'] . "<br>";
    }
} else {
    echo "Error: " . $conn->error . "<br>";
}

// 4. Check deleted_at
echo "<h3>Deleted_at check:</h3>";
if (in_array('deleted_at', $columns)) {
    $sql = "SELECT COUNT(*) as count FROM products WHERE deleted_at IS NULL";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    echo "Products where deleted_at IS NULL: " . $row['count'] . "<br>";
    
    $sql = "SELECT COUNT(*) as count FROM products WHERE deleted_at IS NOT NULL";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();
    echo "Products where deleted_at IS NOT NULL: " . $row['count'] . "<br>";
} else {
    echo "Column 'deleted_at' does NOT exist!<br>";
}

// 5. Test the specific problematic query
echo "<h3>Testing Featured Products Query:</h3>";
$featured_sql = "SELECT p.id, p.name, p.status, p.featured FROM products p WHERE p.status = 'active' AND p.featured = 1 AND p.deleted_at IS NULL";
$res = $conn->query($featured_sql);
if ($res) {
    echo "Query executed successfully.<br>";
    echo "Rows found: " . $res->num_rows . "<br>";
    while ($row = $res->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - " . $row['name'] . "<br>";
    }
} else {
    echo "Query Error: " . $conn->error . "<br>";
}
?>
