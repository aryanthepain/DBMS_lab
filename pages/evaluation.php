<?php
// File: pages/evaluation.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];

// Retrieve completed exam bookings for this student.
$stmt = $pdo->prepare("SELECT booking_ID, Exam_ID FROM takes_exam WHERE Roll_number = ? AND end_time IS NOT NULL ORDER BY booking_ID DESC");
$stmt->execute([$roll]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (empty($bookings)) {
    die("No completed exam found for evaluation.");
}
$selectedBookingID = isset($_GET['bookingID']) ? $_GET['bookingID'] : $bookings[0]['booking_ID'];
$stmt = $pdo->prepare("SELECT Exam_ID FROM takes_exam WHERE booking_ID = ?");
$stmt->execute([$selectedBookingID]);
$examData = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$examData) {
    die("Invalid booking selected.");
}
$examID = $examData['Exam_ID'];

// Fetch evaluation details.
$stmt = $pdo->prepare("
    SELECT er.QID, q.question, er.selected_option, er.is_correct, 
           TIMESTAMPDIFF(SECOND, er.start_time, er.end_time) AS time_spent,
           IFNULL(f.feedback_text, '') AS feedback_text, q.difficulty
    FROM exam_results er
    JOIN questions q ON er.QID = q.QID
    LEFT JOIN feedback f ON er.booking_ID = f.booking_ID AND er.QID = f.QID
    WHERE er.booking_ID = ?
");
$stmt->execute([$selectedBookingID]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate metrics.
$totalQuestions = count($results);
$correctCount = 0;
$totalTime = 0;
$difficultyCounts = [];
foreach ($results as $row) {
    if ($row['is_correct']) {
        $correctCount++;
    }
    $totalTime += $row['time_spent'];
    $diff = $row['difficulty'];
    if (!isset($difficultyCounts[$diff])) {
        $difficultyCounts[$diff] = 0;
    }
    $difficultyCounts[$diff]++;
}
$scorePercentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
$avgTimePerQuestion = $totalQuestions > 0 ? round($totalTime / $totalQuestions, 2) : 0;

// Additional metrics.
$stmt = $pdo->prepare("SELECT COUNT(DISTINCT Roll_number) FROM takes_exam WHERE Exam_ID = ? AND end_time IS NOT NULL");
$stmt->execute([$examID]);
$studentCount = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT MAX(correct_count) as highest, MIN(correct_count) as lowest FROM (
       SELECT booking_ID, SUM(is_correct) as correct_count FROM exam_results
       WHERE QID IN (SELECT QID FROM in_exam WHERE Exam_ID = ?)
       GROUP BY booking_ID
    ) sub");
$stmt->execute([$examID]);
$scoreData = $stmt->fetch(PDO::FETCH_ASSOC);

$analysisResult = [
    'avg_score' => round($scorePercentage, 2),
    'avg_time' => $avgTimePerQuestion,
    'studentCount' => $studentCount,
    'highest_score' => $scoreData['highest'] ?? 0,
    'lowest_score' => $scoreData['lowest'] ?? 0
];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Evaluation & Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Exam Evaluation & Analysis</h2>
        <form method="get" action="evaluation.php">
            <div class="mb-3">
                <label for="bookingID" class="form-label">Select Exam Booking</label>
                <select name="bookingID" id="bookingID" class="form-select" onchange="this.form.submit()">
                    <?php foreach ($bookings as $b): ?>
                        <option value="<?php echo $b['booking_ID']; ?>" <?php if ($selectedBookingID == $b['booking_ID']) echo 'selected'; ?>>
                            Booking <?php echo $b['booking_ID']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Question ID</th>
                    <th>Question</th>
                    <th>Your Answer</th>
                    <th>Correct</th>
                    <th>Time Spent (sec)</th>
                    <th>Feedback</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['QID']); ?></td>
                        <td><?php echo htmlspecialchars($row['question']); ?></td>
                        <td><?php echo htmlspecialchars($row['selected_option']); ?></td>
                        <td><?php echo ($row['is_correct']) ? "Yes" : "No"; ?></td>
                        <td><?php echo htmlspecialchars($row['time_spent']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_text']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <div class="mt-4">
            <h4>Metrics</h4>
            <p><strong>Score Percentage:</strong> <?php echo $analysisResult['avg_score']; ?>%</p>
            <p><strong>Average Time per Question:</strong> <?php echo $analysisResult['avg_time']; ?> sec</p>
            <p><strong>Number of Students Who Took This Exam:</strong> <?php echo $analysisResult['studentCount']; ?></p>
            <p><strong>Highest Score:</strong> <?php echo $analysisResult['highest_score']; ?></p>
            <p><strong>Lowest Score:</strong> <?php echo $analysisResult['lowest_score']; ?></p>
            <h5>Difficulty Breakdown:</h5>
            <?php foreach ($difficultyCounts as $level => $count): ?>
                <p>Difficulty <?php echo $level; ?>: <?php echo $count; ?> question(s)</p>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>