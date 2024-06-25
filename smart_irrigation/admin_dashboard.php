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

// Fetch total users
$totalUsersQuery = "SELECT COUNT(*) as total_users FROM users";
$totalUsersResult = $conn->query($totalUsersQuery);
$totalUsers = $totalUsersResult->fetch_assoc()['total_users'];

// Fetch crop data
$cropDataQuery = "
    SELECT crops.name as crop, COUNT(users.id) as count 
    FROM users 
    INNER JOIN crops ON users.crop_id = crops.id 
    GROUP BY users.crop_id";
$cropDataResult = $conn->query($cropDataQuery);

$crops = [];
$cropCounts = [];

while ($row = $cropDataResult->fetch_assoc()) {
    $crops[] = $row['crop'];
    $cropCounts[] = $row['count'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .sidebar { width: 250px; background: #2ecc71; color: white; position: fixed; height: 100%; padding-top: 20px; }
        .sidebar a { display: block; color: white; padding: 16px; text-decoration: none; }
        .sidebar a:hover { background: #27ae60; }
        .sidebar a i { margin-right: 10px; }
        .main { margin-left: 260px; padding: 20px; }
        .card { background: #f8f9fa; padding: 20px; margin: 10px 0; border-radius: 8px; }
        .card h3 { margin: 0 0 10px; }
        .card-container { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
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
        <a href="admin_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="view_users.php"><i class="fas fa-users"></i> View Users</a>
        <a href="update_users.php"><i class="fas fa-user-edit"></i> Update User</a>
        <a href="#" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main">
        <h1>Admin Dashboard</h1>
        <p>Welcome, Admin!</p>

        <div class="card-container">
            <div class="card">
                <h3>Total Users</h3>
                <p><?php echo $totalUsers; ?></p>
            </div>
            <div class="card">
                <h3>Monthly Water Usage</h3>
                <p>12000 liters</p>
            </div>
            <div class="card">
                <h3>System Health</h3>
                <p>Operational</p>
            </div>
        </div>

        <h2>Crop Distribution</h2>
        <canvas id="cropChart"></canvas>
    </div>

    <div class="popup-bg" id="popupBg"></div>
    <div class="popup" id="popup">
        <h2>Confirm Logout</h2>
        <p>Are you sure you want to log out?</p>
        <button id="confirmLogout">Yes</button>
        <button id="cancelLogout">No</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const crops = <?php echo json_encode($crops); ?>;
            const cropCounts = <?php echo json_encode($cropCounts); ?>;

            const ctx = document.getElementById('cropChart').getContext('2d');
            const cropChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: crops,
                    datasets: [{
                        label: 'Number of Users',
                        data: cropCounts,
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            const logoutBtn = document.getElementById('logoutBtn');
            const popup = document.getElementById('popup');
            const popupBg = document.getElementById('popupBg');
            const confirmLogout = document.getElementById('confirmLogout');
            const cancelLogout = document.getElementById('cancelLogout');

            logoutBtn.addEventListener('click', function() {
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
        });
    </script>
</body>
</html>
