<?php
// File: pages/questions.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll']) || !isset($_SESSION['booking_ID'])) {
    header("Location: login.php");
    exit();
}

$bookingID = $_SESSION['booking_ID'];
$currentDiff = $_SESSION['current_difficulty'] ?? 1;
$answeredQuestions = []; // collect QIDs that have been answered
// Optionally, you may fetch answered questions from exam_results table:
$stmt = $pdo->prepare("SELECT QID FROM exam_results WHERE booking_ID = ?");
$stmt->execute([$bookingID]);
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $answeredQuestions[] = $row['QID'];
}
$answeredPlaceholders = '';
if (count($answeredQuestions) > 0) {
    $answeredPlaceholders = implode(',', array_fill(0, count($answeredQuestions), '?'));
}

// Query a question with the current difficulty that has not been answered.
if (count($answeredQuestions) > 0) {
    $sql = "SELECT * FROM questions WHERE difficulty = ? AND QID NOT IN ($answeredPlaceholders) ORDER BY RAND() LIMIT 1";
    $params = array_merge([$currentDiff], $answeredQuestions);
} else {
    $sql = "SELECT * FROM questions WHERE difficulty = ? ORDER BY RAND() LIMIT 1";
    $params = [$currentDiff];
}
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$question = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$question) {
    // No question found at this difficulty level; exam may be over.
    header("Location: evaluation.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Exam Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script>
        // Start a timer for this question (in seconds)
        let questionStartTime = Date.now();

        function getQuestionTime() {
            return Math.floor((Date.now() - questionStartTime) / 1000);
        }
    </script>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h3>Question (Difficulty Level: <?php echo htmlspecialchars($currentDiff); ?>)</h3>
        <p><?php echo htmlspecialchars($question['question']); ?></p>
        <form action="process_exam.php" method="post" onsubmit="document.getElementById('time_spent').value = getQuestionTime();">
            <input type="hidden" name="QID" value="<?php echo htmlspecialchars($question['QID']); ?>">
            <!-- Record the exam session booking_ID -->
            <input type="hidden" name="booking_ID" value="<?php echo htmlspecialchars($bookingID); ?>">
            <!-- Hidden field to capture time spent on the question -->
            <input type="hidden" name="time_spent" id="time_spent" value="0">
            <div class="mb-3">
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
            </div>
            <button type="submit" class="btn btn-primary">Submit Answer</button>
            <a href="process_quit_exam.php" class="btn btn-danger">Quit Exam</a>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>