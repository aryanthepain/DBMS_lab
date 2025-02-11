<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
require_once '../include/dbh.inc.php';

// Fetch available exams
$stmt = $pdo->prepare("SELECT Exam_ID, name FROM exam");
$stmt->execute();
$exams = $stmt->fetchAll();

// Fetch available time slots
$stmt = $pdo->prepare("SELECT slot_ID, start_time, duration FROM slot");
$stmt->execute();
$slots = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Book Exam on Slot</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Book an Exam on a Time Slot</h2>
        <form action="process_book_exam_slot.php" method="POST">
            <div class="mb-3">
                <label for="examID" class="form-label">Select Exam</label>
                <select class="form-select" id="examID" name="examID" required>
                    <option value="">Choose an exam</option>
                    <?php foreach ($exams as $exam): ?>
                        <option value="<?php echo $exam['Exam_ID']; ?>"><?php echo htmlspecialchars($exam['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="slotID" class="form-label">Select Time Slot</label>
                <select class="form-select" id="slotID" name="slotID" required>
                    <option value="">Choose a time slot</option>
                    <?php foreach ($slots as $slot): ?>
                        <option value="<?php echo $slot['slot_ID']; ?>">
                            <?php echo "Slot " . $slot['slot_ID'] . " - " . $slot['start_time'] . " (" . $slot['duration'] . " min)"; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Book Exam on Slot</button>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>