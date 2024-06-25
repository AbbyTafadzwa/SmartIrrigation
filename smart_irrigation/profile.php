<?php
session_start();

$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "smart_irrigation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $user['password'];

    $update_query = "UPDATE users SET fullname = ?, username = ?, phone = ?, location = ?, password = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("sssssi", $fullname, $username, $phone, $location, $password, $user_id);
    $update_stmt->execute();

    header("Location: profile.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Irrigation - Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .sidebar { width: 250px; background: #2ecc71; color: white; position: fixed; height: 100%; padding-top: 20px; }
        .sidebar a { display: block; color: white; padding: 16px; text-decoration: none; }
        .sidebar a:hover { background: #27ae60; }
        .sidebar a i { margin-right: 10px; }
        .main { margin-left: 260px; padding: 20px; }
        .profile-form { max-width: 600px; margin: auto; background: #f8f9fa; padding: 20px; border-radius: 8px; }
        .profile-form h3 { margin: 0 0 10px; }
        .profile-form label { display: block; margin: 10px 0 5px; }
        .profile-form input { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 4px; }
        .profile-form button { background: #2ecc71; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        .popup {
            display: none;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #2ecc71;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            z-index: 1001;
            padding: 20px;
            text-align: center;
        }
        .popup h2 { margin-top: 0; }
        .popup button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 5px;
            cursor: pointer;
        }
        .popup button:hover {
            background-color: #27ae60;
        }
        .popup-bg {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="stats.php" id="checkMoistureBtn"><i class="fas fa-chart-bar"></i> Statistics</a>
        <a href="profile.php" id="updateDetailsBtn"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main">
        <h1>Profile</h1>
        <div class="profile-form">
            <h3>Update Your Details</h3>
            <form method="POST" action="profile.php">
                <label for="fullname">Full Name</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                
                <label for="location">Location</label>
                <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location']); ?>" required>
                
                <label for="password">Password (leave blank to keep current password)</label>
                <input type="password" id="password" name="password">
                
                <button type="submit">Update</button>
            </form>
        </div>
    </div>

    <div class="popup-bg" id="popupBg"></div>
    <div class="popup" id="popup">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to log out?</p>
        <button id="confirmLogout">Yes</button>
        <button id="cancelLogout">No</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const logoutBtn = document.getElementById('logoutBtn');
            const popup = document.getElementById('popup');
            const popupBg = document.getElementById('popupBg');
            const confirmLogout = document.getElementById('confirmLogout');
            const cancelLogout = document.getElementById('cancelLogout');

            logoutBtn.addEventListener('click', function(event) {
                event.preventDefault();
                popup.style.display = 'block';
                popupBg.style.display = 'block';
            });

            cancelLogout.addEventListener('click', function() {
                popup.style.display = 'none';
                popupBg.style.display = 'none';
            });

            confirmLogout.addEventListener('click', function() {
                window.location.href = 'logout.php';
            });

            document.getElementById('initiateIrrigationBtn').addEventListener('click', function() {
                alert('Irrigation initiated.');
            });

            document.getElementById('cancelIrrigationBtn').addEventListener('click', function() {
                alert('Ongoing irrigation canceled.');
            });
        });
    </script>
</body>
</html>
