<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Irrigation</title>
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
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table, th, td { border: 1px solid #ddd; }
        th, td { padding: 12px; text-align: left; }
        th { background-color: #f2f2f2; }
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
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }
        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .slider {
            background-color: #2ecc71;
        }
        input:checked + .slider:before {
            transform: translateX(26px);
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
        <h1>Smart Irrigation</h1>
        <p>Welcome!</p>

        <div class="card-container">
            <div class="card">
                <h3>Moisture Level</h3>
                <p>312</p>
            </div>
            <div class="card">
                <h3>Pump Control</h3>
                <label class="switch">
                    <input type="checkbox" id="pumpControlSwitch">
                    <span class="slider"></span>
                </label>
            </div>
            <div class="card">
                <h3>Today's Irrigation</h3>
                <p>Water Used: 500 liters</p>
            </div>
            <div class="card">
                <h3>Monthly Total</h3>
                <p>Water Used: 7000 liters</p>
            </div>
        </div>

        <h2>Daily Irrigation History</h2>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Transaction Type</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>07:30:22</td>
                    <td>Initiate</td>
                    <td>Morning Irrigation</td>
                    <td>500 liters</td>
                    <td>Completed</td>
                    <td><a href="#">Edit</a></td>
                </tr>
                <tr>
                    <td>15:05:45</td>
                    <td>Cancel</td>
                    <td>Evening Irrigation</td>
                    <td>300 liters</td>
                    <td>Canceled</td>
                    <td><a href="#">Edit</a></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="popup-bg" id="popupBg">
        <div class="popup" id="popup">
            <h2>Popup Title</h2>
            <p>Popup content goes here.</p>
            <button onclick="closePopup()">Close</button>
        </div>
    </div>

    <script>
document.getElementById('pumpControlSwitch').addEventListener('change', function() {
    let command = this.checked ? 'turn_on' : 'turn_off';
    fetch('http://127.0.0.1:1880/irrigation', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ command: command })
    })
    .then(response => response.json())
    .then(data => console.log(data))
    .catch(error => console.error('Error:', error));
});

function closePopup() {
    document.getElementById('popupBg').style.display = 'none';
}

function showPopup() {
    document.getElementById('popupBg').style.display = 'block';
}
</script>

</body>
</html>
