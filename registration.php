<?php
require 'assets/includes/constants.php';
require 'assets/includes/functions.php';
session_start();

if (!isset($_SESSION['device'])) {
    echo "<script>alert('Please log in.')</script>";
    header("Location: auth.php"); 
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST as $key => $value) {
        $_POST[$key] = _cleaninjections(trim($value));
    }

    $con = new mysqli($HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME); 
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    if (isset($_POST['register'])) {
        $plant_name = mysqli_real_escape_string($con, clean_input($_POST['plant-name']));
        $location = mysqli_real_escape_string($con, clean_input($_POST['location']));
        $num_days = intval($_POST['num-days']);

        $insQuery = "INSERT INTO active_devices (device_id, plant_name, location, num_days, soil_moisture, temperature, humidity, rain_chance) VALUES ('" .
        $_SESSION['device'] . "', '$plant_name', '$location', $num_days, 0, 0, 0, 0);";

        if ($con->query($insQuery) === TRUE) {
            echo "<script>alert('Registration successful');</script>";
            header("Location: index.php"); 
            exit();
        } else {
            echo "<script>alert('Registration failed');</script>";
        }
    }

    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plant Registration</title>
    <link rel="stylesheet" href="assets/css/style1.css">
</head>
<body>
    <div class="container">
        <form action="registration.php" method="post">
            <h2>Plant Registration Page</h2>

            <div class="grid-container">
                <div class="pname">
                    <label for="pname">Plant Name</label>
                    <select id="pname" name="plant-name" required>
                        <option hidden selected>- select an option -</option>
                        <option value="sugarcane">Sugarcane</option>
                        <option value="rice">Rice</option>
                        <option value="wheat">Wheat</option>
                        <option value="bajra">Bajra</option>
                    </select>
                </div>

                <div class="location">
                    <label for="location">Location</label>
                    <select id="location" name="location" required>
                        <option hidden selected>- select a Location -</option>
                        <option value="Vidharba">Vidharba</option>
                        <option value="Konkan">Konkan</option>
                        <option value="Western Maharashtra">Western Maharashtra</option>
                        <option value="Northern Maharashtra">Northern Maharashtra</option>
                    </select>
                </div>

                <div class="days">
                    <label for="days">Days</label>
                    <input type="number" id="days" name="num-days" required>
                </div>

                <div class="register">
                    <button type="submit" name="register">Register</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
