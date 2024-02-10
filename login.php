<?php
// Start the session
session_start();

$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "MAP"; // specify the name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the username and password from the POST data
    $username = $_POST['uname'];
    $password = $_POST['psw'];

    // Prepare a SQL statement to select the user with the given username and password
    $stmt = $conn->prepare('SELECT * FROM users WHERE username = ? AND password = ?');
    $stmt->bind_param('ss', $username, $password);

    // Execute the statement
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if a user is found
    if ($result->num_rows > 0) {
        // If a user is found, set the username and user ID in the session and echo 'success'
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_id'] = $user['id'];
        echo 'success';
    } else {
        // If no user is found, echo 'fail'
        echo 'fail';
    }

    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}
?>
