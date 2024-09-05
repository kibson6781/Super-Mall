<?php
require 'assets/includes/constants.php';
session_start();

if (!isset($_SESSION['device'])) {
    echo "<script>alert('Login'); window.location.href='auth';</script>";
    exit();
} else {
    $con = new mysqli($HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }

    $getQuery = "SELECT * FROM active_devices WHERE device_id = '" . $_SESSION['device'] . "';";
    $res = $con->query($getQuery);

    if ($res->num_rows == 1) {
        $data = $res->fetch_assoc();
        $plant_name = $data['plant_name'];
        $location = $data['location'];
        $growth_stage = $data['growth_stage'];
        $num_days = $data['num_days'];
        $soil_moisture = $data['soil_moisture'];
        $temperature = $data['temperature'];
        $humidity = $data['humidity'];
        $rain_chance = $data['rain_chance'];
    } else {
        echo "<script>alert('Register your device.'); window.location.href='register';</script>";
        exit();
    }

    $con->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://unpkg.com/tailwindcss@^1.0/dist/tailwind.min.css" rel="stylesheet">
    <link rel="icon" href="./assets/images/Logo.png">
    <title>I2S - where tech meets farms</title>
    <link rel="stylesheet" href="./assets/css/dashboard_style.css">
    <link rel="stylesheet" href="./assets/css/Response.css">
</head>

<body>
    <!-- Header Part -->
    <header>
        <div class="logosec">
            <div class="logo">I2S</div>
            <img src="./assets/images/Dash.png" class="icn menuicn" id="menuicn" alt="menu-icon">
        </div>
        <div class="message">
            <div class="circle"></div>
            <img src="./assets/images/notification.png" class="icn" alt="notifications">
            <div class="dp">
                <img src="./assets/images/sugarcane2.png" class="dpicn" alt="profile-pic">
            </div>
        </div>
    </header>

    <div class="main-container">
        <div class="navcontainer">
            <nav class="nav">
                <div class="nav-upper-options">
                    <div class="nav-option option1">
                        <img src="./assets/images/dashboard.png" class="nav-img" alt="dashboard">
                        <h3>Dashboard</h3>
                    </div>
                    <div class="nav-option logout" onclick="confirmLogout()">
                        <img src="./assets/images/logout.png" class="nav-img" alt="logout">
                        <h3>Logout</h3>
                    </div>
                </div>
            </nav>
        </div>

        <div class="main">
            <div class="searchbar2"></div>
            <div class="box-container">
                <div class="box box1">
                    <div class="text">
                        <?php echo "<h2 class='topic-heading'>$rain_chance%</h2>"; ?>
                        <h2 class="topic">Chances of Rain</h2>
                    </div>
                    <img src="./assets/images/rain.png" alt="rain">
                </div>
                <div class="box box2">
                    <div class="text">
                        <?php echo "<h2 class='topic-heading'>$humidity%</h2>"; ?>
                        <h2 class="topic">Humidity Level</h2>
                    </div>
                    <img src="./assets/images/Warn.png" alt="humidity">
                </div>
                <div class="box box3">
                    <div class="text">
                        <?php echo "<h2 class='topic-heading'>$soil_moisture%</h2>"; ?>
                        <h2 class="topic">Moisture Level</h2>
                    </div>
                    <img src="./assets/images/Moisture.webp" alt="moisture">
                </div>
                <div class="box box4">
                    <div class="text">
                        <?php echo "<h2 class='topic-heading'>$temperature °C</h2>"; ?>
                        <h2 class="topic">Temperature</h2>
                    </div>
                    <img src="./assets/images/temperature.png" alt="temperature">
                </div>
            </div>

            <div class="report-container">
                <div class="plant-info">
                    <div class="plant-image-container">
                        <img src="./assets/images/sugarcane2.png" alt="Plant image" class="plant-image">
                    </div>
                    <div class="plant-grid">
                        <div class="grid-item">
                            <h3 class="grid-title">Normal</h3>
                            <p class="grid-subtitle">Plant Condition</p>
                        </div>
                        <div class="grid-item">
                            <?php echo "<h3 class='grid-title'>$growth_stage</h3>"; ?>
                            <p class="grid-subtitle">Growth Stage</p>
                        </div>
                        <div class="grid-item">
                            <?php echo "<h3 class='grid-title'>$location</h3>"; ?>
                            <p class="grid-subtitle">Location</p>
                        </div>
                        <div class="grid-item">
                            <?php echo "<h3 class='grid-title'>$num_days</h3>"; ?>
                            <p class="grid-subtitle">Age</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="text-gray-600 body-font">
        <div class="container px-5 py-8 mx-auto flex items-center sm:flex-row flex-col">
            <a class="flex title-font font-medium items-center md:justify-start justify-center text-gray-900">
                <img src="./assets/images/Logo.png" alt="Logo" class="w-15 h-15 text-white p-2 bg-green-500 rounded-full">
                <span class="ml-3 text-xl">Tetra Tribe Ltd</span>
            </a>
            <p class="text-sm text-gray-500 sm:ml-4 sm:pl-4 sm:border-l-2 sm:border-gray-200 sm:py-2 sm:mt-0 mt-4">
                © 2024 Team Tetra Tribe
            </p>
            <span class="inline-flex sm:ml-auto sm:mt-0 mt-4 justify-center sm:justify-start">
                <a class="text-gray-500" href="#">
                    <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M18 2h-3a5 5 0 00-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 011-1h3z"></path>
                    </svg>
                </a>
                <a class="ml-3 text-gray-500" href="#">
                    <svg fill="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M23 3a10.9 10.9 0 01-3.14 1.53 4.48 4.48 0 00-7.86 3v1A10.66 10.66 0 013 4s-4 9 5 13a11.64 11.64 0 01-7 2c9 5 20 0 20-11.5a4.5 4.5 0 00-.08-.83A7.72 7.72 0 0023 3z"></path>
                    </svg>
                </a>
                <a class="ml-3 text-gray-500" href="#">
                    <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" class="w-5 h-5" viewBox="0 0 24 24">
                        <rect width="20" height="20" x="2" y="2" rx="5"></rect>
                        <path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"></path>
                    </svg>
                </a>
            </span>
        </div>
    </footer>

    <script src="./assets/js/iot.js"></script>
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php'; // Adjust logout URL as needed
            }
        }
    </script>
</body>

</html>
