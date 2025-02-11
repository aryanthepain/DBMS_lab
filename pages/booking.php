<?php
session_start();
require_once '../include/dbh.inc.php';

// Fetch available exams from the exam table
$stmt = $pdo->prepare("SELECT Exam_ID, name FROM exam");
$stmt->execute();
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Booking and Fees</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Exam Booking and Fees</h2>
        <form action="process_booking.php" method="POST">
            <div class="mb-3">
                <label for="examID" class="form-label">Select Exam</label>
                <select class="form-select" id="examID" name="examID" required>
                    <option value="">Choose an exam</option>
                    <?php foreach ($exams as $exam): ?>
                        <option value="<?php echo $exam['Exam_ID']; ?>">
                            <?php echo htmlspecialchars($exam['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="transactionID" class="form-label">Transaction ID (Placeholder until payment portal can be linked)</label>
                <input type="text" class="form-control" id="transactionID" name="transactionID" required>
            </div>
            <button type="submit" class="btn btn-primary">Book Exam</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>