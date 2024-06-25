<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_irrigation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username === 'Admin' && $password === 'admin123') {
        $_SESSION['admin'] = true;
        header("Location: admin_dashboard.php");
        exit();
    }

    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];

        // Fetch crop ID from the database
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT crop_id FROM users WHERE user_id = '$user_id'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $crop_id = $row['crop_id'];

            // Send crop ID to Arduino via Node-RED HTTP request
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://localhost:1880/sendCropID");
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "crop_id=$crop_id");
            curl_exec($ch);
            curl_close($ch);
        }

        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if (!empty($error_message)): ?>
        <div class="error-message">
            <p><?php echo $error_message; ?></p>
            <button class="error-button" onclick="document.querySelector('.error-message').style.display='none'">Okay</button>
        </div>
        <?php endif; ?>
        <form id="loginForm" action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
