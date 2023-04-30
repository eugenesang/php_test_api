<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// import environment variables
require_once('env.php');

// retrieve user and hotel data from POST request
$user = $_POST['user'];
$hotel = $_POST['hotel'];

// check if user and hotel are both numbers
if(!$user){
    http_response_code(400);
    $response = array(
        "success" => false,
        "message" => "Please enter a valid user ID"
    );
    echo json_encode($response);
    return;
}
if(!$hotel){
    http_response_code(400);
    $response = array(
        "success" => false,
        "message" => "Please enter a valid hotel ID"
    );
    echo json_encode($response);
    return;
}
if (!is_numeric($user) || !is_numeric($hotel)) {
    http_response_code(400);
    $response = array(
        "success" => false,
        "message" => "User and hotel data must be numbers"
    );
    echo json_encode($response);
    return;
}

$conn = new mysqli($servername, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
    http_response_code(500);
    $response = array(
        "success" => false,
        "message" => "Database connection error"
    );
    echo json_encode($response);
    exit();
}

// check if user and hotel exist in database
$user_query = "SELECT * FROM users WHERE id = $user";
$user_result = $conn->query($user_query);
if ($user_result->num_rows == 0) {
    http_response_code(404);
    $response = array(
        "success" => false,
        "message" => "User not found"
    );
    echo json_encode($response);
    exit();
}

$hotel_query = "SELECT * FROM hotels WHERE id = $hotel";
$hotel_result = $conn->query($hotel_query);
if ($hotel_result->num_rows == 0) {
    http_response_code(404);
    $response = array(
        "success" => false,
        "message" => "Hotel not found"
    );
    echo json_encode($response);
    exit();
}

// Check if there is a reservation for the specified user and hotel
$order_result = $conn->query("SELECT * FROM orders WHERE user = $user AND hotel = $hotel;");

if ($order_result->num_rows > 0) {
    // Reservation already exists, return error response
    http_response_code(409);
    echo json_encode(array(
        "success" => false,
        "message" => "Reservation already exists for this user and hotel"
    ));
    exit();
} 

// check if hotel has vacant rooms
$hotel_row = $hotel_result->fetch_assoc();
if ($hotel_row['vacant_rooms'] == 0) {
    http_response_code(400);
    $response = array(
        "success" => false,
        "message" => "Hotel has no vacant rooms"
    );
    echo json_encode($response);
    exit;
}

// assign room to user and update hotel vacancy
$room_number = $hotel_row['total_rooms'] - $hotel_row['vacant_rooms'] + 1;
$update_query = "UPDATE hotels SET vacant_rooms = vacant_rooms - 1 WHERE id = $hotel";
$conn->query($update_query);

// construct response JSON object
$response = array(
    "success" => true,
    "message" => "Room assigned successfully",
    "user" => $user,
    "hotel" => $hotel,
    "room" => $room_number
);


// Reservation does not exist, create new reservation
$conn->query("INSERT INTO orders (user, hotel) VALUES ($user, $hotel);");

// Construct success response JSON object
$order_response = array(
    "success" => true,
    "message" => "Reservation created successfully",
    "user" => $user,
    "hotel" => $hotel
);
// encode response as JSON and return
echo json_encode(array(
    "hotel" => $response,
    "order" => $order_response,
    "message" => "Successfully created"
));


?>
