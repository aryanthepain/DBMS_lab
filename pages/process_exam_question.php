<?php
// File: pages/process_exam_question.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'], $_SESSION['booking_ID'], $_SESSION['exam_id'], $_SESSION['question_ids'])) {
    header("Location: login.php");
    exit();
}
$bookingID = $_SESSION['booking_ID'];
$roll = $_SESSION['roll'];

// Validate input.
if (!isset($_POST['QID'], $_POST['selected_option'], $_POST['question_start_time'])) {
    die("Invalid submission.");
}
$QID = $_POST['QID'];
$selectedOption = $_POST['selected_option'];
$questionStartTime = $_POST['question_start_time'];
$questionEndTime = time();
$timeSpent = $questionEndTime - $questionStartTime;

// Retrieve correct option for this question.
$stmt = $pdo->prepare("SELECT correct_option FROM questions WHERE QID = ?");
$stmt->execute([$QID]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$question) {
    die("Invalid question.");
}
$isCorrect = ($selectedOption == $question['correct_option']) ? 1 : 0;

// Insert the result for this question.
$stmt = $pdo->prepare("INSERT INTO exam_results (booking_ID, QID, selected_option, is_correct, start_time, end_time)
                       VALUES (?, ?, ?, ?, FROM_UNIXTIME(?), FROM_UNIXTIME(?))");
$stmt->execute([$bookingID, $QID, $selectedOption, $isCorrect, $questionStartTime, $questionEndTime]);

// Increment the question index and reset the question start time.
$_SESSION['question_index']++;
unset($_SESSION['question_start_time']);

// If all questions have been answered, clear the question list and redirect to evaluation.
if ($_SESSION['question_index'] >= count($_SESSION['question_ids'])) {
    unset($_SESSION['question_ids']);
    unset($_SESSION['question_index']);
    header("Location: evaluation_analysis.php");
    exit();
} else {
    header("Location: questions.php");
    exit();
}
