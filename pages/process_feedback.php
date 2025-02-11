<?php
// File: pages/process_feedback.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingID = $_POST['booking_ID'];
    $QID = $_POST['QID'];
    $feedbackText = $_POST['feedback_text'];
    // Assuming the admin’s EID is stored in session (e.g. $_SESSION['admin_id'])
    $EID = $_SESSION['admin_id'] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO feedback (booking_ID, QID, EID, feedback_text) VALUES (?, ?, ?, ?)");
    $stmt->execute([$bookingID, $QID, $EID, $feedbackText]);

    header("Location: admin_dashboard.php");
    exit();
}
