<?php
// File: pages/schedule.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}

$roll = $_SESSION['roll'];
// Only fetch bookings with end_time IS NULL (i.e. not completed)
$stmt = $pdo->prepare("
    SELECT te.booking_ID, te.Exam_ID, te.slot_ID, e.name AS exam_name 
    FROM takes_exam te 
    JOIN exam e ON te.Exam_ID = e.Exam_ID
    WHERE te.Roll_number = :roll AND te.end_time IS NULL
    ORDER BY te.booking_ID DESC
");
$stmt->bindParam(':roll', $roll, PDO::PARAM_INT);
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Select Exam to Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Select an Exam to Schedule/Reschedule</h2>
        <?php if (count($bookings) == 0): ?>
            <p>You have not booked any exams yet, or all your exams are already completed. Please book an exam first.</p>
        <?php else: ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Exam Name</th>
                        <th>Current Slot</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_ID']); ?></td>
                            <td><?php echo htmlspecialchars($booking['exam_name']); ?></td>
                            <td>
                                <?php
                                if ($booking['slot_ID']) {
                                    echo "Slot " . htmlspecialchars($booking['slot_ID']);
                                } else {
                                    echo "Not scheduled";
                                }
                                ?>
                            </td>
                            <td>
                                <a href="schedule.php?bookingID=<?php echo $booking['booking_ID']; ?>" class="btn btn-primary">Schedule/Reschedule</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>