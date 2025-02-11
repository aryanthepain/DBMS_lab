<?php
// File: pages/exam_portal.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];

// Query available exam bookings for this student.
// (Here we assume an exam booking is “active” if its end_time is NULL or still in the future.)
$stmt = $pdo->prepare("
    SELECT t.booking_ID, e.name AS exam_name, s.start_time, s.duration 
    FROM takes_exam t 
    JOIN exam e ON t.Exam_ID = e.Exam_ID 
    JOIN slot s ON t.slot_ID = s.slot_ID 
    WHERE t.Roll_number = ? AND (t.end_time IS NULL OR t.end_time > NOW())
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
        #timer {
            font-size: 2rem;
            font-weight: bold;
        }

        .exam-instructions {
            font-size: 1.1rem;
            margin-bottom: 30px;
        }

        video,
        canvas {
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5 text-center">
        <h2>Exam Portal</h2>
        <p class="exam-instructions">
            Please read the instructions carefully before starting your exam.
            Once you press the "Start Exam" button, your exam timer will begin.
            A snapshot of your ID will be captured for verification.
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
                        <option value="<?php echo htmlspecialchars($booking['booking_ID']); ?>">
                            <?php echo htmlspecialchars($booking['exam_name'] . " (Slot: " . $startDateTime . ", Duration: " . $booking['duration'] . " mins)"); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Exam Timer -->
            <div class="mb-3">
                <h4>Exam Timer: <span id="timer">00:00:00</span></h4>
            </div>

            <!-- Video and Canvas for Photo Capture -->
            <div class="mb-3">
                <video id="video" width="320" height="240" autoplay></video>
                <canvas id="canvas" width="320" height="240" style="display: none;"></canvas>
            </div>

            <!-- Start Exam Form -->
            <form id="startExamForm" action="process_exam_start.php" method="post">
                <!-- Hidden input to store captured photo data -->
                <input type="hidden" name="captured_photo" id="captured_photo">
                <!-- Hidden field to send the selected exam booking -->
                <input type="hidden" name="booking_ID" id="selected_booking_ID">
                <button type="submit" id="startExamBtn" class="btn btn-success btn-lg">Start Exam</button>
            </form>
        <?php endif; ?>
    </div>

    <script>
        // Timer (counts up; actual exam time is controlled later on the questions page)
        let seconds = 0;

        function updateTimer() {
            seconds++;
            let hrs = Math.floor(seconds / 3600);
            let mins = Math.floor((seconds % 3600) / 60);
            let secs = seconds % 60;
            document.getElementById('timer').innerText =
                String(hrs).padStart(2, '0') + ":" +
                String(mins).padStart(2, '0') + ":" +
                String(secs).padStart(2, '0');
        }
        setInterval(updateTimer, 1000);

        // Access the webcam
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const capturedPhotoInput = document.getElementById('captured_photo');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({
                video: true
            }).then(function(stream) {
                video.srcObject = stream;
                video.play();
            }).catch(function(error) {
                console.log("Camera error:", error);
                // If no camera access, assign a flag ("random") to trigger a fallback.
                capturedPhotoInput.value = 'random';
            });
        } else {
            capturedPhotoInput.value = 'random';
        }

        // When the exam is started, capture the photo and include the selected exam booking.
        document.getElementById('startExamForm').addEventListener('submit', function(e) {
            // Set the selected booking ID from the dropdown.
            const selectedBooking = document.getElementById('booking_ID').value;
            document.getElementById('selected_booking_ID').value = selectedBooking;
            // If the camera is available, capture an image.
            if (capturedPhotoInput.value !== 'random') {
                let context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                let dataURL = canvas.toDataURL('image/png');
                capturedPhotoInput.value = dataURL;
            }
            // Optionally, you can stop the video stream here.
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>