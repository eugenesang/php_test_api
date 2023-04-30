<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// import environment variables
require_once('env.php');

// Get user ID and email from request
$user_id = $_POST['id'];
$email = $_POST['email'];

// Connect to database
$conn = new mysqli($servername, $username, $db_password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ? AND email = ?");
$stmt->bind_param("is", $user_id, $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    // User does not exist
    http_response_code(400);
    die("User does not exist");
}

// Get all orders by user ID
$stmt = $conn->prepare("SELECT * FROM orders WHERE user = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Get hotels for each order and add to array
$hotels = array();
while ($row = $result->fetch_assoc()) {
    $hotel_id = $row['hotel'];
    $stmt = $conn->prepare("SELECT * FROM hotels WHERE id = ?");
    $stmt->bind_param("i", $hotel_id);
    $stmt->execute();
    $hotel_result = $stmt->get_result();
    $hotel = $hotel_result->fetch_assoc();
    array_push($hotels, $hotel);
}

// Send back hotels as JSON response
echo json_encode($hotels);

// Close the connection
$conn->close();
?>
