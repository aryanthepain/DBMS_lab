<?php
// File: pages/process_exam_start.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];

// Retrieve selected booking and exam IDs from POST.
if (!isset($_POST['booking_ID']) || !isset($_POST['exam_ID'])) {
    die("No exam booking selected. Please go back and choose an exam.");
}
$bookingID = $_POST['booking_ID'];
$examID = $_POST['exam_ID'];
$_SESSION['booking_ID'] = $bookingID;
$_SESSION['exam_id'] = $examID;

// Process uploaded photo.
$defaultPhoto = 'DEFAULT_BASE64_IMAGE_STRING_HERE'; // Replace with your default base64 encoded image string.
$photoData = $defaultPhoto; // Use default if no file uploaded.
if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
    $photoData = file_get_contents($_FILES['photo']['tmp_name']);
}
// Update student's photo in the database.
$stmt = $pdo->prepare("UPDATE students SET photo = ? WHERE Roll_number = ?");
$stmt->execute([$photoData, $roll]);

// Redirect to the questions page.
header("Location: questions.php");
exit();
