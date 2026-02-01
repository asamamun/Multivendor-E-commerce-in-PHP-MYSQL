<?php
// only ajax methods can access this file, so, check httprequest only
if (
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    http_response_code(403);
    exit('Direct access not allowed');
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['em'])){
    $email = $_POST['em'];
    require "../db/db.php";
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? limit 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows > 0) {
        echo json_encode(array("status" => "error", "message" => "Email already exists"));
    } else {
        echo json_encode(array("status" => "success", "message" => "Email is available"));
    }
    $stmt->close();
    $conn->close();
}





