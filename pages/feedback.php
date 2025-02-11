<?php
// File: pages/feedback.php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Provide Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h3>Provide Feedback for a Candidate's Answer</h3>
        <form action="process_feedback.php" method="post">
            <div class="mb-3">
                <label for="booking_ID" class="form-label">Booking ID</label>
                <input type="number" class="form-control" name="booking_ID" required>
            </div>
            <div class="mb-3">
                <label for="QID" class="form-label">Question ID</label>
                <input type="number" class="form-control" name="QID" required>
            </div>
            <div class="mb-3">
                <label for="feedback_text" class="form-label">Feedback</label>
                <textarea class="form-control" name="feedback_text" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>