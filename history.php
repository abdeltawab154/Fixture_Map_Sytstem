<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "MAP";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Get the machineCode from the user input
$machineCode = $_POST['machineCode'] ?? '';

// Prepare and bind
$stmt = $conn->prepare("SELECT id, operation, machineCode, timestamp FROM history WHERE machineCode LIKE ?");
$stmt->bind_param('s', $machineCode);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

echo "<style>
    body {
        background-color: #f0f0f0;
        font-family: Arial, sans-serif;
    }
    .container {
        width: 80%;
        margin: auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0px 0px 10px #ccc;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    th, td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #4CAF50;
        color: white;
    }
    tr:nth-child(even) {
        background-color: #f2f2f2;
    }
    .form-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>";

echo "<div class='container'>";
echo "<h2>Fixture MAP History</h2>";

echo "<div class='form-container'>";
echo "<form method='post'>";
echo "<input type='text' name='machineCode' placeholder='Enter Fixture code'>";
echo "<input type='submit' value='Search'>";
echo "</form>";
echo "<button onclick=\"location.href='home.html'\" type=\"button\">
        Fixture Map
      </button>";
echo "</div>";

echo "<table>";
echo "<tr><th>Fixture Code</th><th>operation</th><th>Time</th></tr>";

if ($result->num_rows > 0) {
  // output data of each row
  while($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($row['machineCode']) . "</td>";
    echo "<td>" . htmlspecialchars($row['operation']) . "</td>";
    echo "<td>" . htmlspecialchars($row['timestamp']) . "</td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='4'>No results</td></tr>";
}

echo "</table>";
echo "</div>";

$stmt->close();
$conn->close();
?>
