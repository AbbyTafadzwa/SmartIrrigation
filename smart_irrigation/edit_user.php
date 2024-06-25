<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_irrigation";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['user_id'])) {
    echo "User ID is required.";
    exit();
}

$user_id = $_GET['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $phone = $_POST['phone'];
    $location = $_POST['location'];
    $crop = $_POST['crop'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "UPDATE users SET fullname = ?, username = ?, phone = ?, location = ?, crop = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssssi", $fullname, $username, $phone, $location, $crop, $hashed_password, $user_id);
    } else {
        $query = "UPDATE users SET fullname = ?, username = ?, phone = ?, location = ?, crop = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssssi", $fullname, $username, $phone, $location, $crop, $user_id);
    }
    if ($stmt->execute()) {
        echo "User updated successfully.";
    } else {
        echo "Error updating user: " . $conn->error;
    }
}

$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .sidebar { width: 250px; background: #2ecc71; color: white; position: fixed; height: 100%; padding-top: 20px; }
        .sidebar a { display: block; color: white; padding: 16px; text-decoration: none; }
        .sidebar a:hover { background: #27ae60; }
        .sidebar a i { margin-right: 10px; }
        .main { margin-left: 260px; padding: 20px; }
        form { max-width: 600px; margin: 0 auto; }
        label { display: block; margin: 10px 0 5px; }
        input { width: 100%; padding: 10px; margin: 5px 0 10px; border: 1px solid #ddd; border-radius: 5px; }
        button { background: #2ecc71; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        button:hover { background: #27ae60; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="view_users.php"><i class="fas fa-users"></i> View Users</a>
        <a href="update_users.php"><i class="fas fa-user-edit"></i> Update User</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main">
        <h1>Edit User</h1>
        <form action="edit_user.php?user_id=<?php echo htmlspecialchars($user_id); ?>" method="POST">
            <label for="fullname">Full Name:</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>

            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

            <label for="location">Location:</label>
            <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($user['location']); ?>" required>

            <label for="crop">Crop:</label>
            <input type="text" id="crop" name="crop" value="<?php echo htmlspecialchars($user['crop']); ?>" required>

            <label for="password">Password (leave blank to keep current password):</label>
            <input type="password" id="password" name="password">

            <button type="submit">Update User</button>
        </form>
    </div>
</body>
</html>
