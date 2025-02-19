<?php
// File: pages/admin_dashboard.php
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
        <h2>Admin Dashboard</h2>
        <p>Welcome to the admin portal. Manage exams and review detailed performance metrics using the Manage Exam page.</p>
        <div class="list-group">
            <a href="manage_exam.php" class="list-group-item list-group-item-action">Manage Exam (Create Exam, Add Questions, Feedback, Admins, Time Slots, Analysis)</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>