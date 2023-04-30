<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// import environment variables
require_once('env.php');

// connect to database
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// check if there are any vacant rooms in the hotel
$sql = "SELECT * FROM hotels WHERE vacant_rooms > 0";
$result = $conn->query($sql);

// construct response JSON object
$response = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $hotel = array(
            "id" => $row["id"],
            "name" => $row["name"],
            "location" => $row["location"],
            "price" => $row["price"],
            "vacant_rooms" => $row["vacant_rooms"],
            "total_rooms" => $row["total_rooms"],
            "image" => $row["image"]
        );
        array_push($response, $hotel);
    }
}

// encode response as JSON and return
echo json_encode($response);

// close database connection
$conn->close();

?>
