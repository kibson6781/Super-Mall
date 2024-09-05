<?php
require '../assets/includes/functions.php';
require '../assets/includes/constants.php';

$con = new mysqli($HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

// Function to validate and sanitize inputs
function validate_input($data) {
    return htmlspecialchars(trim($data));
}

// Handling POST requests
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Securing against Header Injection
    foreach($_POST as $key => $value) {
        $_POST[$key] = validate_input($value);
    }

    $devId = $_POST['device-id'];
    $password = $_POST['password'];
    
    if (empty($devId) || empty($password)) {
        echo "Device ID or Password cannot be empty.";
        exit();
    }

    $searchQuery = "SELECT * FROM devices WHERE device_id = '$devId' AND password = '$password'";
    $res = $con->query($searchQuery);

    if ($res->num_rows != 1) {
        echo "Invalid Device ID or Password.";
        exit();
    }

    $growth_stage = $_POST['growth_stage'];
    $soil_moisture = $_POST['soil_moisture'];
    $temperature = $_POST['temperature'];
    $humidity = $_POST['humidity'];
    $rain_chance = $_POST['rain_chance'];

    if (empty($growth_stage) || empty($soil_moisture) ||
        empty($temperature) || empty($humidity) || empty($rain_chance)) {
        echo "All fields are required.";
        exit();
    }

    $updateQuery = "UPDATE active_devices SET 
        growth_stage='$growth_stage', 
        soil_moisture=$soil_moisture, 
        temperature=$temperature, 
        humidity=$humidity, 
        rain_chance=$rain_chance 
        WHERE device_id = '$devId'";
        
    if ($con->query($updateQuery) === TRUE) {
        echo "Update successful.";
    } else {
        echo "Error updating record: " . $con->error;
    }
}

// Handling GET requests
else if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Securing against Header Injection
    foreach($_GET as $key => $value) {
        $_GET[$key] = validate_input($value);
    }

    $devId = $_GET['device-id'];
    $password = $_GET['password'];
    
    if (empty($devId) || empty($password)) {
        echo "Device ID or Password cannot be empty.";
        exit();
    }

    $searchQuery = "SELECT * FROM devices WHERE device_id = '$devId' AND password = '$password'";
    $res = $con->query($searchQuery);

    if ($res->num_rows != 1) {
        echo "Invalid Device ID or Password.";
        exit();
    }

    $growth_stage = $_GET['growth_stage'];
    $soil_moisture = intval($_GET['soil_moisture']);
    $temperature = intval($_GET['temperature']);
    $humidity = intval($_GET['humidity']);
    $rain_chance = floatval($_GET['rain_chance']);

    if (empty($growth_stage) || empty($soil_moisture) ||
        empty($temperature) || empty($humidity) || empty($rain_chance)) {
        echo "All fields are required.";
        exit();
    }

    $updateQuery = "UPDATE active_devices SET 
        growth_stage='$growth_stage', 
        soil_moisture=$soil_moisture, 
        temperature=$temperature, 
        humidity=$humidity, 
        rain_chance=$rain_chance 
        WHERE device_id = '$devId'";
        
    if ($con->query($updateQuery) === TRUE) {
        echo "Update successful.";
    } else {
        echo "Error updating record: " . $con->error;
    }
}

$con->close();
?>
