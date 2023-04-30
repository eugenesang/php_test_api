<?php 
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$response = array(
    "success" => true,
    "message" => "Api endpoint was successfully connected"
);

echo json_encode($response);

exit;
?>