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

$message = "";
$crops = [];

$sql = "SELECT id, name FROM crops";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $crops[] = $row;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $crop_id = $_POST['crop_id'];
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirmPassword'];

    if ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (fullname, username, phone, location, crop_id, password)
                VALUES ('$fullname', '$username', '$phone', '$location', '$crop_id', '$hashedPassword')";

        if ($conn->query($sql) === TRUE) {
            $_SESSION['username'] = $username; 
            header("Location: dashboard.php"); 
            exit();
        } else {
            $message = "Error: " . $sql . "<br>" . $conn->error;
        }

        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function validateForm() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            if (password !== confirmPassword) {
                alert('Passwords do not match.');
                return false;
            }
            return true;
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if ($message) echo "<p>$message</p>"; ?>
        <form id="registerForm" action="register.php" method="POST" onsubmit="return validateForm();">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" required>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="phone">Phone Number:</label>
            <input type="tel" id="phone" name="phone" required>
            <label for="location">Location:</label>
            <input type="text" id="location" name="location" required>
            <label for="crop_id">Crop Type:</label>
            <select id="crop_id" name="crop_id" required>
                <?php foreach ($crops as $crop): ?>
                    <option value="<?php echo $crop['id']; ?>"><?php echo $crop['name']; ?></option>
                <?php endforeach; ?>
            </select>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
            <label for="confirmPassword">Confirm Password:</label>
            <input type="password" id="confirmPassword" name="confirmPassword" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
