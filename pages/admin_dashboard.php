<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Admin Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>!</h2>
        <p>This is your admin dashboard. Use the links below to manage exams, review student performance, and adjust settings.</p>
        <div class="list-group">
            <a href="exam_registration.php" class="list-group-item list-group-item-action">Register Exam</a>
            <a href="create_timeslot.php" class="list-group-item list-group-item-action">Create Time Slot</a>
            <a href="book_exam_slot.php" class="list-group-item list-group-item-action">Book Exam Slot</a>
            <a href="manage_questions.php" class="list-group-item list-group-item-action">Manage Questions</a>
            <a href="feedback.php" class="list-group-item list-group-item-action">Provide Feedback</a>
            <a href="dashboard_analysis.php" class="list-group-item list-group-item-action">Analysis Dashboard</a>
            <!-- Add more admin-only functions as needed -->
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>