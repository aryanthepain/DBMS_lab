<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
require_once '../include/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_time = $_POST['start_time'];
    $duration = $_POST['duration'];

    try {
        $stmt = $pdo->prepare("INSERT INTO slot (start_time, duration) VALUES (:start_time, :duration)");
        $stmt->bindParam(':start_time', $start_time);
        $stmt->bindParam(':duration', $duration, PDO::PARAM_INT);
        $stmt->execute();
        $slotID = $pdo->lastInsertId();
        echo "<p>Time slot created successfully. Slot ID: $slotID</p>";
        echo "<p><a href='admin_dashboard.php'>Return to Admin Dashboard</a></p>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
