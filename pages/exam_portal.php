<?php
// File: pages/exam_portal.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];

// Query available exam bookings for this student (only active ones).
$stmt = $pdo->prepare("
    SELECT t.booking_ID, e.Exam_ID, e.name AS exam_name, s.start_time, s.duration 
    FROM takes_exam t 
    JOIN exam e ON t.Exam_ID = e.Exam_ID 
    JOIN slot s ON t.slot_ID = s.slot_ID 
    WHERE t.Roll_number = ? AND t.end_time IS NULL
    ORDER BY s.start_time
");
$stmt->execute([$roll]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .exam-instructions {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5 text-center">
        <h2>Exam Portal</h2>
        <p class="exam-instructions">
            Please read the instructions carefully before starting your exam.
            Once you press the "Start Exam" button, your exam will begin.
            Please upload a photo for exam verification.
        </p>
        <?php if (empty($bookings)) : ?>
            <div class="alert alert-warning">
                <strong>No exam booking available.</strong> Please book an exam slot and try again.
            </div>
        <?php else : ?>
            <!-- Exam Booking Selection -->
            <div class="mb-3">
                <label for="booking_ID" class="form-label">Select Exam to Start</label>
                <select name="booking_ID" id="booking_ID" class="form-select">
                    <?php foreach ($bookings as $booking):
                        // Format start time nicely.
                        $startDateTime = (new DateTime($booking['start_time']))->format("Y-m-d H:i:s");
                    ?>
                        <option value="<?php echo htmlspecialchars($booking['booking_ID']); ?>"
                            data-examid="<?php echo htmlspecialchars($booking['Exam_ID']); ?>">
                            <?php echo htmlspecialchars($booking['exam_name'] . " (Slot: " . $startDateTime . ", Duration: " . $booking['duration'] . " mins)"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Photo Upload -->
            <div class="mb-3">
                <label for="photo" class="form-label">Upload Your Photo</label>
                <input type="file" name="photo" id="photo" accept="image/*" class="form-control">
                <small class="form-text text-muted">If no photo is uploaded, a default image will be used.</small>
            </div>
            <!-- Start Exam Form -->
            <form id="startExamForm" action="process_exam_start.php" method="post" enctype="multipart/form-data">
                <!-- Hidden fields for selected booking and exam ID -->
                <input type="hidden" name="booking_ID" id="selected_booking_ID">
                <input type="hidden" name="exam_ID" id="selected_exam_ID">
                <button type="submit" id="startExamBtn" class="btn btn-success btn-lg">Start Exam</button>
            </form>
        <?php endif; ?>
    </div>
    <script>
        // When the exam is started, set the selected booking and exam id in hidden inputs.
        document.getElementById('startExamForm').addEventListener('submit', function(e) {
            const bookingSelect = document.getElementById('booking_ID');
            const selectedOption = bookingSelect.options[bookingSelect.selectedIndex];
            document.getElementById('selected_booking_ID').value = selectedOption.value;
            document.getElementById('selected_exam_ID').value = selectedOption.getAttribute('data-examid');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>