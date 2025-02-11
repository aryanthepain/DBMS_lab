<?php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}

$roll = $_SESSION['roll'];
$stmt = $pdo->prepare("SELECT booking_ID FROM takes_exam WHERE Roll_number = :roll ORDER BY booking_ID DESC LIMIT 1");
$stmt->bindParam(':roll', $roll, PDO::PARAM_INT);
$stmt->execute();
$booking = $stmt->fetch();

if (!$booking) {
    $message = "No exam booking found for evaluation.";
} else {
    $bookingID = $booking['booking_ID'];
    $stmt = $pdo->prepare("
        SELECT er.QID, q.question, er.selected_option, er.is_correct, 
               TIMESTAMPDIFF(SECOND, er.start_time, er.end_time) AS time_spent,
               f.feedback_text
        FROM exam_results er
        JOIN questions q ON er.QID = q.QID
        LEFT JOIN feedback f ON er.booking_ID = f.booking_ID AND er.QID = f.QID
        WHERE er.booking_ID = :bookingID
    ");
    $stmt->bindParam(':bookingID', $bookingID, PDO::PARAM_INT);
    $stmt->execute();
    $results = $stmt->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Evaluation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Exam Evaluation</h2>
        <?php if (isset($message)) {
            echo "<p>$message</p>";
        } else { ?>
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
        <?php } ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>