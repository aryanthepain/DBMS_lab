<?php
// File: pages/process_quit_exam.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'], $_SESSION['booking_ID'])) {
    header("Location: login.php");
    exit();
}

$bookingID = $_SESSION['booking_ID'];
// You might update the exam session record to indicate the exam was paused.
// For example, set end_time (or add a status field in the future).
$stmt = $pdo->prepare("UPDATE takes_exam SET end_time = NOW() WHERE booking_ID = ?");
$stmt->execute([$bookingID]);

// Clear exam-related session variables (so they can resume later, if desired)
unset($_SESSION['booking_ID']);
unset($_SESSION['current_difficulty']);
unset($_SESSION['question_count']);

// Redirect to the student dashboard with a message.
$_SESSION['message'] = "Exam session saved. You may resume your exam later.";
header("Location: dashboard.php");
exit();
