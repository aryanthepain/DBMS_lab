<?php
// File: pages/evaluation_analysis.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];

// Retrieve all completed exam bookings for the student.
$stmt = $pdo->prepare("SELECT booking_ID, Exam_ID FROM takes_exam WHERE Roll_number = ? AND end_time IS NOT NULL ORDER BY booking_ID DESC");
$stmt->execute([$roll]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Let the student choose a booking.
$selectedBookingID = isset($_GET['bookingID']) ? $_GET['bookingID'] : (count($bookings) > 0 ? $bookings[0]['booking_ID'] : null);
if (!$selectedBookingID) {
    die("No completed exam found for evaluation.");
}

// Fetch evaluation details for the selected booking.
$stmt = $pdo->prepare("
    SELECT er.QID, q.question, er.selected_option, er.is_correct, 
           TIMESTAMPDIFF(SECOND, er.start_time, er.end_time) AS time_spent,
           f.feedback_text, q.difficulty
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
    $difficulty = $row['difficulty'];
    if (!isset($difficultyCounts[$difficulty])) {
        $difficultyCounts[$difficulty] = 0;
    }
    $difficultyCounts[$difficulty]++;
}
$scorePercentage = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
$avgTimePerQuestion = $totalQuestions > 0 ? round($totalTime / $totalQuestions, 2) : 0;

// For demonstration, calculate percentile among all completed exams.
$stmt = $pdo->query("SELECT COUNT(*) FROM takes_exam WHERE end_time IS NOT NULL");
$totalExams = $stmt->fetchColumn();
$stmt = $pdo->prepare("SELECT COUNT(*) FROM takes_exam 
                       WHERE (SELECT COUNT(*) FROM exam_results er WHERE er.booking_ID = takes_exam.booking_ID AND er.is_correct = 1) >= ?");
$stmt->execute([$correctCount]);
$examsAbove = $stmt->fetchColumn();
$percentile = $totalExams > 0 ? round(($examsAbove / $totalExams) * 100, 2) : 0;
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
        <!-- Booking selection -->
        <form method="get" action="evaluation_analysis.php">
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

        <!-- Evaluation Table -->
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

        <!-- Metrics -->
        <div class="mt-4">
            <h4>Metrics</h4>
            <p><strong>Score Percentage:</strong> <?php echo $scorePercentage; ?>%</p>
            <p><strong>Average Time per Question:</strong> <?php echo $avgTimePerQuestion; ?> sec</p>
            <p><strong>Percentile:</strong> <?php echo $percentile; ?>%</p>
            <h5>Difficulty Breakdown:</h5>
            <?php foreach ($difficultyCounts as $level => $count): ?>
                <p>Difficulty <?php echo $level; ?>: <?php echo $count; ?> question(s)</p>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>