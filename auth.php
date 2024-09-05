<?php
require 'assets/includes/constants.php';
require 'assets/includes/functions.php';
session_start();

$con = new mysqli($HOSTNAME, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);
if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Securing against Header Injection
    foreach ($_POST as $key => $value) {
        $_POST[$key] = _cleaninjections(trim($value));
    }

    $devId = mysqli_real_escape_string($con, clean_input($_POST['device-id']));
    $password = mysqli_real_escape_string($con, trim($_POST['password']));

    if (isset($_POST['login-btn'])) {
        $q1 = "SELECT device_id FROM devices WHERE device_id = '$devId'";
        $res = $con->query($q1);

        if ($res->num_rows == 1) {
            $q2 = "SELECT * FROM users WHERE device_id = '$devId'";
            $res2 = $con->query($q2);

            if ($res2->num_rows == 1) {
                $corr_pass = $res2->fetch_assoc()['password'];
                
                if ($password === $corr_pass) {
                    $_SESSION['device'] = $devId;
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<script>alert('Incorrect password!')</script>";
                }
            } else {
                echo "<script>alert('Device not registered. Go to signup tab to register.')</script>";
            }
        } else {
            echo "<script>alert('Incorrect Device ID!')</script>";
        }
    } else if (isset($_POST['signup-btn'])) {
        $email = mysqli_real_escape_string($con, filter_var(clean_input($_POST['email']), FILTER_SANITIZE_EMAIL));

        $q1 = "SELECT device_id FROM devices WHERE device_id = '$devId'";
        $res = $con->query($q1);

        if ($res->num_rows == 1) {
            $q2 = "SELECT device_id FROM users WHERE device_id = '$devId'";
            $res2 = $con->query($q2);

            if ($res2->num_rows == 0) {
                $insQuery = "INSERT INTO users (device_id, email, password) VALUES ('$devId', '$email', '$password')";

                if ($con->query($insQuery) === TRUE) {
                    $_SESSION['device'] = $devId;
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<script>alert('Signup failed.')</script>";
                }
            } else {
                echo "Device already registered. Proceed to login.";
            }
        } else {
            echo "<script>alert('Device with entered ID does not exist!')</script>";
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
    <title>Login/Signup</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="cont">
        <div class="form sign-in">
            <h2>Log In</h2>
            <form method="post" action="auth.php">
                <label>
                    <span>Device ID</span>
                    <input type="text" name="device-id" required>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" required>
                </label>
                <button class="submit" id="login-btn" type="submit" name="login-btn">Log In</button>
            </form>
            <p class="forgot-pass">Forgot Password?</p>
            <div class="social-media">
                <ul>
                    <li><a href="#"><img src="assets/images/facebook.png" alt="Facebook"></a></li>
                    <li><a href="#"><img src="assets/images/twitter.png" alt="Twitter"></a></li>
                    <li><a href="#"><img src="assets/images/linkedin.png" alt="LinkedIn"></a></li>
                    <li><a href="#"><img src="assets/images/instagram.png" alt="Instagram"></a></li>
                </ul>
            </div>
        </div>

        <div class="sub-cont">
            <div class="img">
                <div class="img-text m-up">
                    <h2>New to our Site?</h2>
                    <p>Welcome, be part of our mission to connect farmers and technology!</p>
                </div>
                <div class="img-text m-in">
                    <h2>One of us?</h2>
                    <p>Already has an account? Welcome Back! We missed you!</p>
                </div>
                <div class="img-btn">
                    <span class="m-up">Sign Up</span>
                    <span class="m-in">Log In</span>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

