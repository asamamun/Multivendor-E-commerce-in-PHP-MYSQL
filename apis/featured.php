<?php
require __DIR__ . '/helpers.php';
require __DIR__ . '/../db/db.php';
$q = "select * from products where featured=1";
$result = $conn->query($q);
//fetch all
$rows  = $result->fetch_all(MYSQLI_ASSOC);
respond_success($rows);
