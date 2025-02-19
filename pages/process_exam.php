<?php
// File: pages/process_exam.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'], $_SESSION['booking_ID'], $_SESSION['exam_id'])) {
    header("Location: login.php");
    exit();
}
$bookingID = $_SESSION['booking_ID'];
$examID = $_SESSION['exam_id'];
$roll = $_SESSION['roll'];

// Expecting answers as an associative array: questions[QID][selected_option]
if (!isset($_POST['questions']) || !is_array($_POST['questions'])) {
    die("No answers submitted.");
}
$answers = $_POST['questions'];

// Process each answer.
foreach ($answers as $qid => $data) {
    $selectedOption = $data['selected_option'];
    // Retrieve correct option for the question.
    $stmt = $pdo->prepare("SELECT correct_option FROM questions WHERE QID = ?");
    $stmt->execute([$qid]);
    $question = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$question) {
        continue; // Skip invalid question
    }
    $isCorrect = ($selectedOption == $question['correct_option']) ? 1 : 0;
    // Record the exam result.
    $stmt = $pdo->prepare("INSERT INTO exam_results (booking_ID, QID, selected_option, is_correct, start_time, end_time)
                           VALUES (?, ?, ?, ?, NOW(), NOW())");
    $stmt->execute([$bookingID, $qid, $selectedOption, $isCorrect]);
}

// Mark exam as completed.
$stmt = $pdo->prepare("UPDATE takes_exam SET end_time = NOW() WHERE booking_ID = ?");
$stmt->execute([$bookingID]);

// Redirect to evaluation page.
header("Location: evaluation.php");
exit();
