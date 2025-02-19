<?php
// File: pages/admin_dashboard.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
require_once '../include/dbh.inc.php';
$admin_id = $_SESSION['eid'];

// Retrieve admin details.
$stmt = $pdo->prepare("SELECT * FROM examiners WHERE EID = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
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
        <h2>Welcome, <?php echo htmlspecialchars($admin['name']); ?>!</h2>
        <div class="row mt-3">
            <div class="col-md-3">
                <!-- Optional: Display admin photo if available -->
                <img src="data:image/png;base64,<?php echo base64_encode($admin['photo'] ?? ''); ?>" alt="Admin Photo" class="img-fluid" onerror="this.src='path/to/default_admin.png'">
            </div>
            <div class="col-md-9">
                <h4>Your Details</h4>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($admin['EID']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($admin['Phone_no']); ?></p>
                <!-- Add more details as needed -->
            </div>
        </div>
        <hr>
        <h3>Admin Features</h3>
        <ul class="list-group">
            <li class="list-group-item"><a href="exam_registration.php">Register Exam</a></li>
            <li class="list-group-item"><a href="create_timeslot.php">Create Time Slot</a></li>
            <li class="list-group-item"><a href="book_exam_slot.php">Book Exam on Slot</a></li>
            <li class="list-group-item"><a href="manage_exam.php">Manage Exam (Create, Add Qs, Feedback, Admins, Slots)</a></li>
            <li class="list-group-item"><a href="exam_analysis.php">View Exam Analysis</a></li>
            <!-- Add more admin feature links as needed -->
        </ul>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>