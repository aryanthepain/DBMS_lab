<?php
// File: pages/process_exam.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'], $_SESSION['booking_ID'])) {
    header("Location: login.php");
    exit();
}

$bookingID = $_SESSION['booking_ID'];
$roll = $_SESSION['roll'];
$QID = $_POST['QID'];
$selectedOption = $_POST['selected_option'];
$timeSpent = intval($_POST['time_spent']);

// Retrieve the question details.
$stmt = $pdo->prepare("SELECT correct_option, difficulty FROM questions WHERE QID = ?");
$stmt->execute([$QID]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    die("Invalid question.");
}

$isCorrect = ($selectedOption == $question['correct_option']) ? 1 : 0;

// Record the exam result with start and end times.
// (For simplicity, we record the current time as end_time. In a real implementation,
// you might record the actual start time for the question.)
$stmt = $pdo->prepare("INSERT INTO exam_results (booking_ID, QID, selected_option, is_correct, start_time, end_time)
                       VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL ? SECOND))");
$stmt->execute([$bookingID, $QID, $selectedOption, $isCorrect, $timeSpent]);

// Adaptive Algorithm: Adjust the difficulty level.
// For example, if the candidate answered correctly and quickly (<30 sec), increase difficulty;
// if not, decrease it (with boundaries 1 and 5).
$currentDiff = $_SESSION['current_difficulty'] ?? 1;
$maxDiff = 5;
$minDiff = 1;
if ($isCorrect && $timeSpent < 30) {
    $currentDiff++;
} else {
    $currentDiff--;
}
$currentDiff = max($minDiff, min($maxDiff, $currentDiff));

// Update session variable for current difficulty.
$_SESSION['current_difficulty'] = $currentDiff;

// Increase question count and check if exam is finished (e.g., 10 questions maximum)
$_SESSION['question_count'] = ($_SESSION['question_count'] ?? 0) + 1;
if ($_SESSION['question_count'] >= 10) {
    // Mark exam end time in takes_exam table (if desired)
    $stmt = $pdo->prepare("UPDATE takes_exam SET end_time = NOW() WHERE booking_ID = ?");
    $stmt->execute([$bookingID]);
    header("Location: evaluation.php");
    exit();
}

// Redirect to the next question.
header("Location: questions.php");
exit();
