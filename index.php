<?php
session_start();
if (!isset($_SESSION['device'])) {
    header("Location: auth.php");
    exit();
}

require 'assets/includes/constants.php';

$con = new mysqli($HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME); 
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

$device_id = $_SESSION['device'];
$q1 = "SELECT * FROM active_devices WHERE device_id = '$device_id'";
$res = $con->query($q1);

if ($res->num_rows > 0) {
    $device_data = $res->fetch_assoc();
} else {
    $device_data = null;
}
$con->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Dashboard</title>
    <link rel="stylesheet" href="assets/css/style2.css">
</head>
<body>
    <h1>Device Dashboard</h1>

    <?php if ($device_data): ?>
        <p><strong>Device ID:</strong> <?php echo htmlspecialchars($device_data['device_id']); ?></p>
        <p><strong>Plant Name:</strong> <?php echo htmlspecialchars($device_data['plant_name']); ?></p>
        <p><strong>Location:</strong> <?php echo htmlspecialchars($device_data['location']); ?></p>
        <p><strong>Days:</strong> <?php echo htmlspecialchars($device_data['num_days']); ?></p>
        <p><strong>Soil Moisture:</strong> <?php echo htmlspecialchars($device_data['soil_moisture']); ?>%</p>
        <p><strong>Temperature:</strong> <?php echo htmlspecialchars($device_data['temperature']); ?>°C</p>
        <p><strong>Humidity:</strong> <?php echo htmlspecialchars($device_data['humidity']); ?>%</p>
        <p><strong>Rain Chance:</strong> <?php echo htmlspecialchars($device_data['rain_chance']); ?>%</p>
    <?php else: ?>
        <p>No data available for this device.</p>
    <?php endif; ?>

    <a href="auth.php">Log Out</a>
</body>
</html>
