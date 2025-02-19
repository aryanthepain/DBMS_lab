<?php
// File: pages/admin_dashboard.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
require_once '../include/dbh.inc.php';
$admin_id = $_SESSION['eid'];
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
    <style>
        .default-admin-img {
            max-width: 150px;
            border-radius: 50%;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Admin Dashboard</h2>
        <div class="row mt-3">
            <div class="col-md-3">
                <img src="../assets/default.png" alt="Default Admin Photo" class="default-admin-img img-fluid">
            </div>
            <div class="col-md-9">
                <h4>Your Details</h4>
                <p><strong>ID:</strong> <?php echo htmlspecialchars($admin['EID']); ?></p>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($admin['name']); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($admin['Phone_no']); ?></p>
            </div>
        </div>
        <hr>
        <h3>Admin Features</h3>
        <p>Use the Manage Exam page to create exams, add questions, assign administrators, manage time slots, and review exam analysis and feedback.</p>
        <a href="manage_exam.php" class="btn btn-primary">Go to Manage Exam</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>