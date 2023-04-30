<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// import environment variables
require_once('env.php');

// Get the values from the form submission
$email = $_POST['email'];
$password = $_POST['password'];
$passwordRepeat = $_POST['password-repeat'];

// Validate the form data
if ($password != $passwordRepeat) {
  die("Passwords do not match");
}

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Connect to the database


$conn = new mysqli($servername, $username, $password, $dbname);

// Check for errors
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Insert the user into the database
$sql = "INSERT INTO users (email, password) VALUES ('$email', '$hashedPassword')";

if ($conn->query($sql) === TRUE) {
  $response = array(
    "success" => TRUE,
    "message" => "Successfully created user"
  );
  echo json_encode($response);
  exit;
} else {
  echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the connection
$conn->close();

?>
