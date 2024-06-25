<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['user_id'])) {
    echo "User ID is required.";
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

$user_id = $_GET['user_id'];
$query = "SELECT * FROM irrigation_reports WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Irrigation Reports</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .sidebar { width: 250px; background: #2ecc71; color: white; position: fixed; height: 100%; padding-top: 20px; }
        .sidebar a { display: block; color: white; padding: 16px; text-decoration: none; }
        .sidebar a:hover { background: #27ae60; }
        .sidebar a i { margin-right: 10px; }
        .main { margin-left: 260px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="sidebar">
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="view_users.php"><i class="fas fa-users"></i> View Users</a>
        <a href="update_user.php"><i class="fas fa-user-edit"></i> Update User</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main">
        <h1>User Irrigation Reports</h1>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Transaction Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($report = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($report['date']); ?></td>
                        <td><?php echo htmlspecialchars($report['transaction_type']); ?></td>
                        <td><?php echo htmlspecialchars($report['description']); ?></td>
                        <td><?php echo htmlspecialchars($report['amount']); ?></td>
                        <td><?php echo htmlspecialchars($report['status']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
