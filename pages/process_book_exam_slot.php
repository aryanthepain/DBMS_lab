<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
require_once '../include/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $examID = $_POST['examID'];
    $slotID = $_POST['slotID'];

    try {
        $stmt = $pdo->prepare("INSERT INTO available_on_slot (slot_ID, Exam_ID) VALUES (:slotID, :examID)");
        $stmt->bindParam(':slotID', $slotID, PDO::PARAM_INT);
        $stmt->bindParam(':examID', $examID, PDO::PARAM_INT);
        $stmt->execute();
        echo "<p>Exam has been booked on the selected time slot successfully.</p>";
        echo "<p><a href='admin_dashboard.php'>Return to Admin Dashboard</a></p>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
