<?php
// File: pages/dashboard.php
session_start();
if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
require_once '../include/dbh.inc.php';
$roll = $_SESSION['roll'];

// Retrieve student details (excluding photo).
$stmt = $pdo->prepare("SELECT Roll_number, name FROM students WHERE Roll_number = ?");
$stmt->execute([$roll]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Student Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .student-photo {
            max-width: 150px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($student['name']); ?>!</h2>
        <div class="row mt-3">
            <div class="col-md-3">
                <!-- Use default photo only -->
                <img src="../assets/default.png" alt="Default Photo" class="student-photo img-fluid">
            </div>
            <div class="col-md-9">
                <h4>Your Details</h4>
                <p><strong>Roll Number:</strong> <?php echo htmlspecialchars($student['Roll_number']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($student['name']); ?></p>
                <!-- Add additional details if needed -->
            </div>
        </div>
        <hr>
        <h3>Available Features</h3>
        <ul class="list-group">
            <li class="list-group-item"><a href="exam_registration.php">Register for Exams</a></li>
            <li class="list-group-item"><a href="booking.php">Book Exam &amp; Pay Fees</a></li>
            <li class="list-group-item"><a href="schedule.php">Schedule/Reschedule Exam Slot</a></li>
            <li class="list-group-item"><a href="exam_portal.php">Take Exam</a></li>
            <li class="list-group-item"><a href="evaluation.php">View Evaluation &amp; Analysis</a></li>
            <!-- Add more feature links as needed -->
        </ul>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>