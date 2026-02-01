<?php
if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    exit('Direct access not allowed');
}
function jahanara($s){
    sleep($s);
}
if(isset($_POST['product_sku'])) {
    // jahanara(5);
    require "../db/db.php";
    $sku = $_POST['product_sku'];
    //check sku in prepared statement
    $stmt = $conn->prepare("SELECT id FROM products WHERE sku = ?");
    $stmt->bind_param("s", $sku);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        echo json_encode(array("status" => "error", "message" => "SKU already exists"));
    } else {
        echo json_encode(array("status" => "success", "message" => "SKU is available"));
    }
    $stmt->close();
    $conn->close();
}