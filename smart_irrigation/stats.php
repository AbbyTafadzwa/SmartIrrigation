<?php
$servername = "localhost";
$username = "root"; // Change if you have a different username
$password = ""; // Change if you have a different password
$dbname = "smart_irrigation";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: ". $conn->connect_error);
}

$sql = "SELECT moisture_level, timestamp FROM soil_moisture ORDER BY timestamp ASC";
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: ". $conn->error);
}

$moistureData = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $moistureData[] = array(
            'moisture_level' => $row['moisture_level'],
            'timestamp' => strtotime($row['timestamp']) * 1000 // Convert to milliseconds
        );
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Irrigation Statistics</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; }
        .sidebar { width: 250px; background: #2ecc71; color: white; position: fixed; height: 100%; padding-top: 20px; }
        .sidebar a { display: block; color: white; padding: 16px; text-decoration: none; }
        .sidebar a:hover { background: #27ae60; }
        .sidebar a i { margin-right: 10px; }
        .main { margin-left: 260px; padding: 20px; }
        #moistureChart { max-width: 100%; height: 400px; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="sidebar">
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="statistics.php" id="checkMoistureBtn"><i class="fas fa-chart-bar"></i> Statistics</a>
        <a href="profile.php" id="updateDetailsBtn"><i class="fas fa-user"></i> Profile</a>
        <a href="logout.php" id="logoutBtn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
    <div class="main">
        <h1>Smart Irrigation</h1>
        <h2>Soil Moisture Levels</h2>
        <canvas id="moistureChart"></canvas>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const moistureData = <?php echo json_encode($moistureData);?>;

        const labels = moistureData.map(entry => entry.timestamp);
        const values = moistureData.map(entry => entry.moisture_level);

        const ctx = document.getElementById('moistureChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Soil Moisture Level',
                    data: values,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    xAxes: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'YYYY-MM-DD HH:mm:ss',
                            displayFormats: {
                                day: 'YYYY-MM-DD'
                            }
                        }
                    },
                    yAxes: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
</body>
</html>
