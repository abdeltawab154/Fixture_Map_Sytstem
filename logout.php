<?php
// Start the session
session_start();

// Specify your MySQL database connection data
$servername = "localhost"; // replace with your database host
$username = "root"; // replace with your database username
$password = "root"; // replace with your database password
$dbname = "MAP"; // replace with your database na
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Unset all of the session variables
$_SESSION = array();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to the login page
header("Location: login.html");
exit;
?>
