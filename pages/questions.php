<?php
// author: aryanthepain
// File: pages/questions.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'], $_SESSION['booking_ID'], $_SESSION['exam_ID'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];
$bookingID = $_SESSION['booking_ID'];
$examID = $_SESSION['exam_ID'];

// Retrieve exam slot info to calculate exam end time.
$stmt = $pdo->prepare("SELECT s.start_time, s.duration FROM slot s JOIN takes_exam t ON s.slot_ID = t.slot_ID WHERE t.booking_ID = ?");
$stmt->execute([$bookingID]);
$slot = $stmt->fetch(PDO::FETCH_ASSOC);
if ($slot) {
    $startTime = new DateTime($slot['start_time']);
    $examEndTime = clone $startTime;
    $examEndTime->modify("+{$slot['duration']} minutes");
} else {
    $examEndTime = (new DateTime())->modify("+10 minutes");
}
$now = new DateTime();
$remainingSeconds = $examEndTime->getTimestamp() - $now->getTimestamp();
if ($remainingSeconds < 0) {
    header("Location: process_quit_exam.php");
    exit();
}

// Use default photo (do not fetch from DB).
$defaultPhotoPath = '../assets/default.png';

// Set up the question list in session if not already set.
if (!isset($_SESSION['question_ids'])) {
    $stmt = $pdo->prepare("SELECT q.QID FROM questions q JOIN in_exam ie ON q.QID = ie.QID WHERE ie.Exam_ID = ?");
    $stmt->execute([$examID]);
    $questionIDs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $_SESSION['question_ids'] = $questionIDs;
    $_SESSION['question_index'] = 0;
    $_SESSION['question_start_time'] = time();
}
$questionIDs = $_SESSION['question_ids'];
$questionIndex = $_SESSION['question_index'];
if ($questionIndex >= count($questionIDs)) {
    unset($_SESSION['question_ids'], $_SESSION['question_index'], $_SESSION['question_start_time']);
    header("Location: evaluation.php");
    exit();
}
$currentQID = $questionIDs[$questionIndex];
$stmt = $pdo->prepare("SELECT * FROM questions WHERE QID = ?");
$stmt->execute([$currentQID]);
$question = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$question) {
    die("Question not found.");
}
if (!isset($_SESSION['question_start_time'])) {
    $_SESSION['question_start_time'] = time();
}
$questionStartTime = $_SESSION['question_start_time'];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        #countdownTimer {
            font-size: 2rem;
            font-weight: bold;
        }

        .student-photo {
            max-width: 150px;
            border-radius: 50%;
            margin-bottom: 20px;
        }
    </style>
    <script>
        // Overall exam countdown timer.
        let remaining = <?php echo $remainingSeconds; ?>;

        function updateTimer() {
            if (remaining <= 0) {
                clearInterval(timerInterval);
                document.getElementById('examForm').submit(); // Auto-submit exam if time expires.
            } else {
                let hrs = Math.floor(remaining / 3600);
                let mins = Math.floor((remaining % 3600) / 60);
                let secs = remaining % 60;
                document.getElementById('countdownTimer').innerText =
                    String(hrs).padStart(2, '0') + ":" +
                    String(mins).padStart(2, '0') + ":" +
                    String(secs).padStart(2, '0');
                remaining--;
            }
        }
        let timerInterval = setInterval(updateTimer, 1000);
    </script>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Exam Question <?php echo $questionIndex + 1; ?> of <?php echo count($questionIDs); ?></h2>
        <!-- Display default student photo -->
        <div class="mb-3">
            <img src="<?php echo $defaultPhotoPath; ?>" alt="Default Photo" class="student-photo">
        </div>
        <!-- Countdown Timer -->
        <div class="mb-3">
            <h4>Time Remaining: <span id="countdownTimer">00:00:00</span></h4>
        </div>
        <form id="examForm" action="process_exam_question.php" method="post">
            <input type="hidden" name="QID" value="<?php echo $question['QID']; ?>">
            <input type="hidden" name="question_start_time" value="<?php echo $questionStartTime; ?>">
            <p><?php echo htmlspecialchars($question['question']); ?></p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="selected_option" value="1" required>
                <label class="form-check-label"><?php echo htmlspecialchars($question['option1']); ?></label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="selected_option" value="2" required>
                <label class="form-check-label"><?php echo htmlspecialchars($question['option2']); ?></label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="selected_option" value="3" required>
                <label class="form-check-label"><?php echo htmlspecialchars($question['option3']); ?></label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="selected_option" value="4" required>
                <label class="form-check-label"><?php echo htmlspecialchars($question['option4']); ?></label>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Next</button>
            <a href="process_quit_exam.php" class="btn btn-danger mt-3">Quit Exam</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>