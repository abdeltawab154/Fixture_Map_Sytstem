<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    // If not, redirect to the login page
    header('Location: login.html');
    exit;
}

// Rest of your code...
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Machine Operations</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/bootstrap.min.css">
  <link rel="stylesheet" href="styles.css">
  <style>
  .navbar-brand {
      font-weight: 1000; /* This will make the text extra bold */
      font-size: 30px; /* This will increase the font size */
      width: 100%; /* This will make the navbar-brand take up the full width of its parent */
      text-align: center; /* This will center the text */
  }
</style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-light bg-light">
  <a class="navbar-brand" href="#">SMD Fixture MAP</a>
  <form class="form-inline" id="logoutForm" action="logout.php" method="post">
    <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Logout</button>
    <!-- History Button -->
    <button class="btn btn-outline-success my-2 my-sm-0 ml-2" type="button" onclick="window.location.href='history.php'">History</button>
  </form>
</nav>

<div class="container-fluid mt-5">

  <!-- Machine Operations -->
  <div class="card-deck mb-3">
    <!-- Add operation -->
    <div class="card">
      <div class="card-header">Add Fixture</div>
      <div class="card-body">
        <form id="addForm" action="machines.php" method="post">
          <input type="hidden" name="operation" value="add">
          <div class="form-group row">
            <div class="col-sm-8">
              <input type="text" class="form-control" id="addMachineCode" placeholder="Enter Fixture code" name="machineCode">
            </div>
            <div class="col-sm-4">
              <button type="submit" class="btn btn-primary">Add</button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Remove operation -->
    <!-- Remove operation -->
    <div class="card">
      <div class="card-header">Remove Fixture</div>
      <div class="card-body">
        <form id="removeForm" action="machines.php" method="post">
          <input type="hidden" name="operation" value="remove">
          <div class="form-group row">
            <div class="col-sm-6">
              <input type="text" class="form-control" id="removeMachineCode" placeholder="Enter Fixture code" name="machineCode">
            </div>
            <div class="col-sm-6">
              <input type="text" class="form-control" id="removeLocation" placeholder="Enter Location" name="location">
            </div>
          </div>
          <div class="form-group row">
            <div class="col-sm-12 text-center"> <!-- Add text-center class -->
              <button type="submit" class="btn btn-primary">Remove</button>
            </div>
          </div>
        </form>
      </div>
    </div>
    <!-- Search operation -->
    <div class="card">
      <div class="card-header">Search Fixture</div>
      <div class="card-body">
        <form id=searchForm action="" method=post>
          <input type=hidden name=operation value=search>
          <div class=form-group row>
            <div class=col-sm-8>
              <input type=text class=form-control id=searchMachineCode placeholder="Enter Fixture code " name=machineCode>
            </div>
            <div class=col-sm-4>
              <button type=submit class=btn btn-primary>Search</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Display the data from the 'Machines' table -->
  <div id=machinesData class=card p-3 style=margin-left:0></div>

</body>

<script src="js/jquery.min.js"></script>

<script>
    // Function to validate the machine code
    function validateMachineCode(machineCode) {
      // Regular expression for the standard "BN41-0XXXXA"
      var regex = /^BN41-0\d{4}A$/;
      return regex.test(machineCode);
    }

    $(document).ready(function(){
      $("#addForm, #removeForm, #searchForm").on("submit", function(event){
        event.preventDefault();

        // Check if a machine code is entered
        var machineCode = $(this).find('input[name=machineCode]').val();
        if (!machineCode) {
          alert("Please enter a Fixture code.");
          return;
        }

        // Validate the machine code
        if (!validateMachineCode(machineCode)) {
          alert("Invalid Fixture code. Please enter a code in the format BN41-0XXXXA.");
          return;
        }

    // Ask for confirmation before sending the request
    if (confirm("Are you sure you want to perform this operation?")) {
      // Send a POST request to the server for add or remove operations
      $.ajax({
        url: "machines.php",
        type: "POST",
        data: $(this).serialize(),
        success: function(data){
          // Refresh the machines data
          $("#machinesData").html(data);
        }
      });
    }
  });

  $("#searchForm").on("submit", function(event){
    event.preventDefault();

    // Check if a machine code is entered
    var machineCode = $(this).find('input[name=machineCode]').val();
    if (!machineCode) {
      alert("Please enter a Fixture code.");
      return;
    }

    // Send a POST request to the server for search operation
    $.ajax({
      url: "machines.php",
      type: "POST",
      data: $(this).serialize(),
      success: function(data){
        // Refresh the machines data with search results
        $("#machinesData").html(data);
      }
    });
  });
});
$(document).ready(function(){
  $("#historyButton").click(function(){
    $.ajax({
      url: 'history.php',
      type: 'get',
      success: function(response){
        // You can do something with the response here
        console.log(response);
      }
    });
  });
});
</script>

<style>
@keyframes highlight {
  0% {background-color: #FFFF00;}
  50% {background-color: #FFFF00;}
  100% {background-color: #28a745;}
}
.highlight {
  animation: highlight 180s forwards;
}
</style>

</html>
