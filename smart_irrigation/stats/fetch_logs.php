<?php
// fetch_logs.php

header('Content-Type: application/json');

$host = 'localhost';
$db = 'your_database_name';
$user = 'your_username';
$pass = 'your_password';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$timeframe = $_GET['timeframe'] ?? 'week';

switch ($timeframe) {
    case 'month':
        $interval = '1 MONTH';
        break;
    case '2months':
        $interval = '2 MONTH';
        break;
    case 'week':
    default:
        $interval = '1 WEEK';
        break;
}

$stmt = $pdo->prepare("SELECT date, transaction_type, description, amount, status FROM irrigation_logs WHERE date >= NOW() - INTERVAL $interval");
$stmt->execute();
$logs = $stmt->fetchAll();

echo json_encode($logs);
?>
