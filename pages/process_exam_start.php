<?php
// File: pages/process_exam_start.php
session_start();
require_once '../include/dbh.inc.php';

// Verify that the student is logged in.
if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];

// Get the captured photo from POST data.
$capturedPhoto = $_POST['captured_photo'];

// If the captured photo is flagged as "random", use a default placeholder image.
if ($capturedPhoto === 'random') {
    $capturedPhoto = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMwAAADACAMAAAB/...'; // Replace with your default image string.
}

// Determine the selected booking ID from POST data.
if (isset($_POST['booking_ID'])) {
    $_SESSION['booking_ID'] = $_POST['booking_ID'];
} else if (!isset($_SESSION['booking_ID'])) {
    die("No exam booking selected. Please go back and choose an exam.");
}
$bookingID = $_SESSION['booking_ID'];

// Update the student's photo in the database. (Alternatively, store it separately.)
$stmt = $pdo->prepare("UPDATE students SET photo = ? WHERE Roll_number = ?");
$stmt->execute([$capturedPhoto, $roll]);

// Retrieve the scheduled slot details for this booking.
$stmt = $pdo->prepare("
    SELECT s.start_time, s.duration 
    FROM slot s 
    JOIN takes_exam t ON s.slot_ID = t.slot_ID 
    WHERE t.booking_ID = ?
");
$stmt->execute([$bookingID]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$slot) {
    die("No valid exam slot is associated with this booking. Please contact the administrator.");
}

// Create DateTime objects for the current time, the scheduled start time, and calculate the end time.
$currentTime = new DateTime("now", new DateTimeZone('Asia/Kolkata'));
echo var_dump($currentTime);
$startTime   = new DateTime($slot['start_time'], new DateTimeZone('Asia/Kolkata'));
echo var_dump($startTime);

// Assume the 'duration' field is in minutes.
$endTime = clone $startTime;
$endTime->modify("+{$slot['duration']} minutes");

// Check whether the current server time is within the scheduled exam time slot.
if ($currentTime < $startTime || $currentTime > $endTime) {
    die("You cannot start the exam at this time.<br>" .
        "Your scheduled slot is from " . $startTime->format("Y-m-d H:i:s") .
        " to " . $endTime->format("Y-m-d H:i:s") . ".<br>" .
        "Please come back during your scheduled time.");
}

// The time check passes; the exam may be started.
// Optionally, reset the difficulty counter or any exam session variables as needed.
$stmt = $pdo->prepare("UPDATE takes_exam SET difficulty_counter = 1 WHERE booking_ID = ?");
$stmt->execute([$bookingID]);

// Initialize session variables for the exam session.
$_SESSION['current_difficulty'] = 1;  // Starting difficulty level
$_SESSION['question_count']     = 0;  // Counter for the number of questions answered

// Redirect the candidate to the adaptive questions page.
header("Location: questions.php");
exit();
