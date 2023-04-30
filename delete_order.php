<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");


// import environment variables
require_once('env.php');

// Get the user id and hotel id from the request
$user_id = $_POST['user'];
$hotel_id = $_POST['hotel'];


// Connect to the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user and hotel exist
$user_query = "SELECT * FROM users WHERE id = '$user_id'";
$hotel_query = "SELECT * FROM hotels WHERE id = '$hotel_id'";

$user_result = $conn->query($user_query);
$hotel_result = $conn->query($hotel_query);

if ($user_result->num_rows == 0 || $hotel_result->num_rows == 0) {
    die("User or hotel does not exist");
}

// Delete the existing order
$order_query = "DELETE FROM orders WHERE user = '$user_id' AND hotel = '$hotel_id'";

if ($conn->query($order_query) === TRUE) {
    echo "Order deleted successfully";
} else {
    echo "Error deleting order: " . $conn->error;
}

// Close the connection
$conn->close();
?>
