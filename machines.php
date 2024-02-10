<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "root";

// Create connection
$conn = new mysqli($servername, $username, $password);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Check if the database exists
$db_selected = mysqli_select_db($conn, 'MAP');

if (!$db_selected) {
  // If the database doesn't exist, create it
  $sql = "CREATE DATABASE MAP";
  if ($conn->query($sql) === TRUE) {
    echo "<div class='center'>Database created successfully</div>";
  } else {
    echo "<div class='center'>Error creating database: " . $conn->error . "</div>";
  }
}

// Select the database
$conn->select_db("MAP");

// Check if the table exists
$sql = "SHOW TABLES LIKE 'Machines'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
  // If the table doesn't exist, create it
  $sql = "CREATE TABLE Machines (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(30) NOT NULL,
  location VARCHAR(30) NOT NULL,
  status VARCHAR(50)
  )";

  if ($conn->query($sql) === TRUE) {
    echo "<div class='center'>Table Machines created successfully</div>";
  } else {
    echo "<div class='center'>Error creating table: " . $conn->error . "</div>";
  }
}

// Check if the history table exists
$sql = "SHOW TABLES LIKE 'History'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
  // If the history table doesn't exist, create it
  $sql = "CREATE TABLE History (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  operation VARCHAR(30) NOT NULL,
  machineCode VARCHAR(30) NOT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
  )";

  if ($conn->query($sql) === TRUE) {
    echo "<div class='center'>Table History created successfully</div>";
  } else {
    echo "<div class='center'>Error creating table: " . $conn->error . "</div>";
  }
}

// Initialize the operation location
$operationLocation = null;

// Get the data from the POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operation = $_POST['operation'];
    $machineCode = $_POST['machineCode'];
    $location = isset($_POST['location']) ? $_POST['location'] : null; // Check if 'location' is set

    if ($operation === 'add') {
      // Check if there is an empty location
      $sql = "SELECT * FROM Machines WHERE status='Empty' LIMIT 1";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $locationCode = $row["location"];

        // Add a machine at the first empty location
        $sql = "UPDATE Machines SET code='$machineCode', status='Full' WHERE location='$locationCode'";

        if ($conn->query($sql) === TRUE) {
          echo "<div class='center'>Machine added successfully at location " . $locationCode . "</div>";

          // Add operation to history table
          $sql = "INSERT INTO History (operation, machineCode)
          VALUES ('add', '$machineCode')";

          if ($conn->query($sql) !== TRUE) {
            echo "<div class='center'>Error: " . $sql . "<br>" . $conn->error . "</div>";
          }

          // Store the operation location
          $operationLocation = $locationCode;
        } else {
          echo "<div class='center'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
      } else {
        echo "<div class='center'>No empty locations available</div>";
      }
    } elseif ($operation === 'remove' && $location !== null) {
      // Check if the machine exists at the specified location
      $sql = "SELECT * FROM Machines WHERE code='$machineCode' AND location='$location'";
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        // Remove a machine from a specific location
        $sql = "UPDATE Machines SET code='Free Location', status='Empty' WHERE code='$machineCode' AND location='$location' LIMIT 1";

        if ($conn->query($sql) === TRUE) {
          echo "<div class='center'>Machine removed successfully from location " . $location . "</div>";

          // Add operation to history table
          $sql = "INSERT INTO History (operation, machineCode)
          VALUES ('remove', '$machineCode')";

          if ($conn->query($sql) !== TRUE) {
            echo "<div class='center'>Error: " . $sql . "<br>" . $conn->error . "</div>";
          }

          // Store the operation location
          $operationLocation = $location;
        } else {
          echo "<div class='center'>Error: " . $sql . "<br>" . $conn->error . "</div>";
        }
      } else {
        echo "<div class='center'>No machine found with code " . $machineCode . " at location " . $location . "</div>";
      }
    }
}

// Fetch all data from the 'Machines' table
$sql = "SELECT * FROM Machines";
$result = $conn->query($sql);

$searchCount = 0;

if (isset($_POST['machineCode'])) {
    // Count the number of machines with the same code
    while($row = $result->fetch_assoc()) {
        if ($row["code"] == $_POST['machineCode']) {
            $searchCount++;
        }
    }

    // Display the number of machines with the same name above the table data
    echo "<div class='center'>Number of machines with code '" . $_POST['machineCode'] . "': " . $searchCount . "</div>";
}

$result = $conn->query($sql);

if ($result->num_rows > 0) {

    // Start the table with added CSS for black borders
    echo "<table class='table' style='border-collapse: collapse; border: 1px solid black;'>";
    echo "<tbody>";

    // Initialize count to zero before using it in loop.
    $count = 0;

    // Output data of each row
    while($row = $result->fetch_assoc()) {

        // Start a new row for every 27 records
        if ($count % 17 == 0) {
            if ($count > 0) {
                echo "</tr>";
            }
            echo "<tr>";
        }

        // Change the background color based on the status, operation location and machine code
        if ($row["location"] == $operationLocation) {
            echo "<td class='highlight' style='background-color: #FFFF00; font-size: 0.8em; border: 1px solid black;'><div>" . $row["code"] . "</div><div>" . $row["location"] . "</div></td>";
        } elseif (isset($_POST['machineCode']) && $row["code"] == $_POST['machineCode'] && $operation !== 'add' && $operation !== 'remove') {
            echo "<td class='highlight' style='background-color: #FFFF00; font-size: 0.8em; border: 1px solid black;'><div>" . $row["code"] . "</div><div>" . $row["location"] . "</div></td>";
        } else {
            $color = $row["status"] == 'Full' ? '#28a745' : '#FFFFFF'; // LightGreen for Full, Pink for Empty
            echo "<td style='background-color: " . $color . "; font-size: 0.8em; border: 1px solid black;'><div>" . $row["code"] . "</div><div>" . $row["location"] . "</div></td>";
        }

        $count++;
    }


    // End the table
    echo "</tr></tbody>";
    echo "</table>";
} else {
    echo "<div class='center'>No machines found</div>";
}

$conn->close();
?>
