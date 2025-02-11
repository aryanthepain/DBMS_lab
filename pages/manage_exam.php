<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}
require_once '../include/dbh.inc.php'; // Assumes $pdo is a valid PDO connection.
$admin_id = $_SESSION['eid'];

// Initialize message variables.
$addQuestionMsg = "";
$feedbackMsg    = "";
$addAdminMsg    = "";
$timeslotMsg    = "";
$analysisResult = [];

// -------------------------
// Process Form Submissions
// -------------------------

if (isset($_POST['action'])) {
    $action = $_POST['action'];
    // (A) Add Question
    if ($action === 'add_question') {
        $exam_id       = $_POST['exam_id'];
        $question_text = $_POST['question_text'];
        $difficulty    = $_POST['difficulty'];
        $option1       = $_POST['option1'];
        $option2       = $_POST['option2'];
        $option3       = $_POST['option3'];
        $option4       = $_POST['option4'];
        $correct_option = $_POST['correct_option'];

        // Insert the new question.
        $stmt = $pdo->prepare("INSERT INTO questions (question, difficulty, option1, option2, option3, option4, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$question_text, $difficulty, $option1, $option2, $option3, $option4, $correct_option])) {
            $new_qid = $pdo->lastInsertId();
            // Link the question to the exam.
            $stmt = $pdo->prepare("INSERT INTO in_exam (QID, Exam_ID) VALUES (?, ?)");
            if ($stmt->execute([$new_qid, $exam_id])) {
                $addQuestionMsg = "Question added successfully!";
            } else {
                $addQuestionMsg = "Question added but failed to link with exam.";
            }
        } else {
            $addQuestionMsg = "Failed to add question.";
        }
    }
    // (B) Submit Feedback
    elseif ($action === 'submit_feedback') {
        $booking_id       = $_POST['booking_id']; // The booking ID for the selected student/exam.
        $feedbackEntries  = $_POST['feedback'];   // Associative array: feedback[QID] => text.
        foreach ($feedbackEntries as $qid => $feedback_text) {
            if (trim($feedback_text) !== "") {
                // Check if feedback already exists.
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM feedback WHERE booking_ID = ? AND QID = ?");
                $stmt->execute([$booking_id, $qid]);
                if ($stmt->fetchColumn() == 0) {
                    $stmt = $pdo->prepare("INSERT INTO feedback (booking_ID, QID, EID, feedback_text) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$booking_id, $qid, $admin_id, $feedback_text]);
                }
            }
        }
        $feedbackMsg = "Feedback submitted successfully!";
    }
    // (C) Add Administrator
    elseif ($action === 'add_admin') {
        $exam_id            = $_POST['exam_id'];
        $new_admin_eid      = $_POST['new_admin_eid'];
        $new_admin_name     = $_POST['new_admin_name'];
        $new_admin_phone    = $_POST['new_admin_phone'];
        $new_admin_password = $_POST['new_admin_password'];

        // Check if the administrator already exists.
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM examiners WHERE EID = ?");
        $stmt->execute([$new_admin_eid]);
        if ($stmt->fetchColumn() == 0) {
            // Insert new administrator.
            $stmt = $pdo->prepare("INSERT INTO examiners (EID, name, Phone_no) VALUES (?, ?, ?)");
            $stmt->execute([$new_admin_eid, $new_admin_name, $new_admin_phone]);
            $stmt = $pdo->prepare("INSERT INTO examiner_password (EID, password) VALUES (?, ?)");
            $stmt->execute([$new_admin_eid, $new_admin_password]);
            $addAdminMsg = "New administrator added! ";
        } else {
            $addAdminMsg = "Administrator already exists. ";
        }
        // Assign the administrator to the exam (if not already assigned).
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM administered_by WHERE EID = ? AND Exam_ID = ?");
        $stmt->execute([$new_admin_eid, $exam_id]);
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO administered_by (EID, Exam_ID) VALUES (?, ?)");
            $stmt->execute([$new_admin_eid, $exam_id]);
            $addAdminMsg .= "Administrator assigned to exam successfully.";
        } else {
            $addAdminMsg .= "Administrator is already assigned to this exam.";
        }
    }
    // (D) Add Time Slot
    elseif ($action === 'add_timeslot') {
        $exam_id    = $_POST['exam_id'];
        $start_time = $_POST['start_time'];
        $duration   = $_POST['duration'];
        // Insert new time slot.
        $stmt = $pdo->prepare("INSERT INTO slot (start_time, duration) VALUES (?, ?)");
        if ($stmt->execute([$start_time, $duration])) {
            $slot_id = $pdo->lastInsertId();
            // Link the time slot to the exam.
            $stmt = $pdo->prepare("INSERT INTO available_on_slot (slot_ID, Exam_ID) VALUES (?, ?)");
            if ($stmt->execute([$slot_id, $exam_id])) {
                $timeslotMsg = "Time slot added successfully!";
            } else {
                $timeslotMsg = "Time slot added but failed to link with exam.";
            }
        } else {
            $timeslotMsg = "Failed to add time slot.";
        }
    }
}

// -------------------------
// Prepare Data for Display
// -------------------------

// Get list of exams administered by this admin.
$stmt = $pdo->prepare("SELECT e.Exam_ID, e.name FROM exam e JOIN administered_by ab ON e.Exam_ID = ab.Exam_ID WHERE ab.EID = ?");
$stmt->execute([$admin_id]);
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For the Feedback section:
// If an exam and student are selected via GET parameters.
$selected_exam_id   = isset($_GET['exam_id']) ? $_GET['exam_id'] : "";
$selected_student_id = isset($_GET['student_id']) ? $_GET['student_id'] : "";
$feedback_data = [];
$booking_id_for_feedback = null;
if ($selected_exam_id && $selected_student_id) {
    // Get the booking ID for the selected exam and student.
    $stmt = $pdo->prepare("SELECT booking_ID FROM takes_exam WHERE Exam_ID = ? AND Roll_number = ?");
    $stmt->execute([$selected_exam_id, $selected_student_id]);
    $booking_row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($booking_row) {
        $booking_id_for_feedback = $booking_row['booking_ID'];
        // Retrieve exam results that lack feedback.
        $stmt = $pdo->prepare("SELECT er.QID, q.question, er.selected_option, q.correct_option, er.start_time, er.end_time, COALESCE(f.feedback_text, '') AS feedback_text
            FROM exam_results er
            JOIN questions q ON er.QID = q.QID
            LEFT JOIN feedback f ON er.booking_ID = f.booking_ID AND er.QID = f.QID
            WHERE er.booking_ID = ? AND (f.feedback_text IS NULL OR f.feedback_text = '')");
        $stmt->execute([$booking_id_for_feedback]);
        $feedback_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

// For Feedback: get the list of students who have taken a selected exam.
$students_in_exam = [];
if ($selected_exam_id) {
    $stmt = $pdo->prepare("SELECT DISTINCT s.Roll_number, s.name 
                           FROM students s
                           JOIN takes_exam t ON s.Roll_number = t.Roll_number
                           WHERE t.Exam_ID = ?");
    $stmt->execute([$selected_exam_id]);
    $students_in_exam = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// For Exam Analysis (optional): if an exam is selected via GET parameter 'analysis_exam_id'.
$analysis_exam_id = isset($_GET['analysis_exam_id']) ? $_GET['analysis_exam_id'] : "";
if ($analysis_exam_id) {
    // Example analysis: average score and average time per question.
    $stmt = $pdo->prepare("SELECT AVG(correct_count) as avg_score FROM (
       SELECT booking_ID, SUM(is_correct) as correct_count FROM exam_results
       WHERE QID IN (SELECT QID FROM in_exam WHERE Exam_ID = ?)
       GROUP BY booking_ID
    ) sub");
    $stmt->execute([$analysis_exam_id]);
    $avg_score = $stmt->fetchColumn();

    $stmt = $pdo->prepare("SELECT AVG(TIMESTAMPDIFF(SECOND, start_time, end_time)) as avg_time FROM exam_results WHERE QID IN (SELECT QID FROM in_exam WHERE Exam_ID = ?)");
    $stmt->execute([$analysis_exam_id]);
    $avg_time = $stmt->fetchColumn();

    $analysisResult = ['avg_score' => round($avg_score, 2), 'avg_time' => round($avg_time, 2)];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Exam</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS v5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Manage Exam</h2>
        <!-- Bootstrap Tabs for different functionalities -->
        <ul class="nav nav-tabs" id="manageExamTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="add-question-tab" data-bs-toggle="tab" data-bs-target="#add-question" type="button" role="tab" aria-controls="add-question" aria-selected="true">Add Question</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="manage-feedback-tab" data-bs-toggle="tab" data-bs-target="#manage-feedback" type="button" role="tab" aria-controls="manage-feedback" aria-selected="false">Manage Feedback</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="add-admin-tab" data-bs-toggle="tab" data-bs-target="#add-admin" type="button" role="tab" aria-controls="add-admin" aria-selected="false">Add Administrator</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="add-timeslot-tab" data-bs-toggle="tab" data-bs-target="#add-timeslot" type="button" role="tab" aria-controls="add-timeslot" aria-selected="false">Add Time Slot</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="exam-analysis-tab" data-bs-toggle="tab" data-bs-target="#exam-analysis" type="button" role="tab" aria-controls="exam-analysis" aria-selected="false">Exam Analysis</button>
            </li>
        </ul>
        <div class="tab-content" id="manageExamTabContent">
            <!-- Add Question Tab -->
            <div class="tab-pane fade show active" id="add-question" role="tabpanel" aria-labelledby="add-question-tab">
                <h3 class="mt-3">Add a New Question</h3>
                <?php if ($addQuestionMsg) echo '<div class="alert alert-success">' . $addQuestionMsg . '</div>'; ?>
                <form method="post" action="manage_exam.php">
                    <input type="hidden" name="action" value="add_question">
                    <div class="mb-3">
                        <label for="exam_id_question" class="form-label">Select Exam</label>
                        <select name="exam_id" id="exam_id_question" class="form-select" required>
                            <option value="">-- Select Exam --</option>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['Exam_ID']; ?>"><?php echo htmlspecialchars($exam['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="question_text" class="form-label">Question</label>
                        <textarea name="question_text" id="question_text" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="difficulty" class="form-label">Difficulty (1-5)</label>
                        <input type="number" name="difficulty" id="difficulty" class="form-control" min="1" max="5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Options</label>
                        <input type="text" name="option1" class="form-control mb-1" placeholder="Option 1" required>
                        <input type="text" name="option2" class="form-control mb-1" placeholder="Option 2" required>
                        <input type="text" name="option3" class="form-control mb-1" placeholder="Option 3" required>
                        <input type="text" name="option4" class="form-control mb-1" placeholder="Option 4" required>
                    </div>
                    <div class="mb-3">
                        <label for="correct_option" class="form-label">Correct Option (1-4)</label>
                        <input type="number" name="correct_option" id="correct_option" class="form-control" min="1" max="4" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Question</button>
                </form>
            </div>
            <!-- Manage Feedback Tab -->
            <div class="tab-pane fade" id="manage-feedback" role="tabpanel" aria-labelledby="manage-feedback-tab">
                <h3 class="mt-3">Manage Feedback</h3>
                <?php if ($feedbackMsg) echo '<div class="alert alert-success">' . $feedbackMsg . '</div>'; ?>
                <!-- Select Exam for Feedback -->
                <form method="get" action="manage_exam.php">
                    <div class="mb-3">
                        <label for="exam_id_feedback" class="form-label">Select Exam</label>
                        <select name="exam_id" id="exam_id_feedback" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Select Exam --</option>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['Exam_ID']; ?>" <?php if ($selected_exam_id == $exam['Exam_ID']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($exam['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <?php if ($selected_exam_id): ?>
                    <!-- Dropdown for students who have taken this exam -->
                    <form method="get" action="manage_exam.php">
                        <input type="hidden" name="exam_id" value="<?php echo $selected_exam_id; ?>">
                        <div class="mb-3">
                            <label for="student_id" class="form-label">Select Student</label>
                            <select name="student_id" id="student_id" class="form-select" required onchange="this.form.submit()">
                                <option value="">-- Select Student --</option>
                                <?php foreach ($students_in_exam as $student): ?>
                                    <option value="<?php echo $student['Roll_number']; ?>" <?php if ($selected_student_id == $student['Roll_number']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($student['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </form>
                    <?php if ($selected_student_id && !empty($feedback_data)): ?>
                        <form method="post" action="manage_exam.php">
                            <input type="hidden" name="action" value="submit_feedback">
                            <input type="hidden" name="booking_id" value="<?php echo $booking_id_for_feedback; ?>">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Question ID</th>
                                        <th>Question</th>
                                        <th>Student Answer</th>
                                        <th>Correct Answer</th>
                                        <th>Feedback</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($feedback_data as $row): ?>
                                        <tr>
                                            <td><?php echo $row['QID']; ?></td>
                                            <td><?php echo htmlspecialchars($row['question']); ?></td>
                                            <td><?php echo htmlspecialchars($row['selected_option']); ?></td>
                                            <td><?php echo ($row['correct_option'] == $row['selected_option']) ? 'Correct' : 'Incorrect'; ?></td>
                                            <td><textarea name="feedback[<?php echo $row['QID']; ?>]" class="form-control" placeholder="Enter feedback"></textarea></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-success">Submit Feedback</button>
                        </form>
                    <?php elseif ($selected_student_id): ?>
                        <div class="alert alert-info">All questions for this student have been given feedback.</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <!-- Add Administrator Tab -->
            <div class="tab-pane fade" id="add-admin" role="tabpanel" aria-labelledby="add-admin-tab">
                <h3 class="mt-3">Add Administrator</h3>
                <?php if ($addAdminMsg) echo '<div class="alert alert-success">' . $addAdminMsg . '</div>'; ?>
                <form method="post" action="manage_exam.php">
                    <input type="hidden" name="action" value="add_admin">
                    <div class="mb-3">
                        <label for="exam_id_admin" class="form-label">Select Exam</label>
                        <select name="exam_id" id="exam_id_admin" class="form-select" required>
                            <option value="">-- Select Exam --</option>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['Exam_ID']; ?>"><?php echo htmlspecialchars($exam['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="new_admin_eid" class="form-label">Administrator EID</label>
                        <input type="number" name="new_admin_eid" id="new_admin_eid" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_admin_name" class="form-label">Administrator Name</label>
                        <input type="text" name="new_admin_name" id="new_admin_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_admin_phone" class="form-label">Phone Number</label>
                        <input type="text" name="new_admin_phone" id="new_admin_phone" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_admin_password" class="form-label">Password</label>
                        <input type="password" name="new_admin_password" id="new_admin_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Administrator</button>
                </form>
            </div>
            <!-- Add Time Slot Tab -->
            <div class="tab-pane fade" id="add-timeslot" role="tabpanel" aria-labelledby="add-timeslot-tab">
                <h3 class="mt-3">Add Time Slot</h3>
                <?php if ($timeslotMsg) echo '<div class="alert alert-success">' . $timeslotMsg . '</div>'; ?>
                <form method="post" action="manage_exam.php">
                    <input type="hidden" name="action" value="add_timeslot">
                    <div class="mb-3">
                        <label for="exam_id_timeslot" class="form-label">Select Exam</label>
                        <select name="exam_id" id="exam_id_timeslot" class="form-select" required>
                            <option value="">-- Select Exam --</option>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['Exam_ID']; ?>"><?php echo htmlspecialchars($exam['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="start_time" class="form-label">Start Time (YYYY-MM-DD HH:MM:SS)</label>
                        <input type="text" name="start_time" id="start_time" class="form-control" placeholder="e.g., 2025-02-15 10:00:00" required>
                    </div>
                    <div class="mb-3">
                        <label for="duration" class="form-label">Duration (minutes)</label>
                        <input type="number" name="duration" id="duration" class="form-control" min="1" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Time Slot</button>
                </form>
            </div>
            <!-- Exam Analysis Tab -->
            <div class="tab-pane fade" id="exam-analysis" role="tabpanel" aria-labelledby="exam-analysis-tab">
                <h3 class="mt-3">Exam Analysis</h3>
                <form method="get" action="manage_exam.php">
                    <div class="mb-3">
                        <label for="analysis_exam_id" class="form-label">Select Exam</label>
                        <select name="analysis_exam_id" id="analysis_exam_id" class="form-select" required onchange="this.form.submit()">
                            <option value="">-- Select Exam --</option>
                            <?php foreach ($exams as $exam): ?>
                                <option value="<?php echo $exam['Exam_ID']; ?>" <?php if ($analysis_exam_id == $exam['Exam_ID']) echo 'selected'; ?>><?php echo htmlspecialchars($exam['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
                <?php if ($analysis_exam_id && !empty($analysisResult)): ?>
                    <div class="alert alert-info">
                        <p><strong>Average Score:</strong> <?php echo $analysisResult['avg_score']; ?></p>
                        <p><strong>Average Time per Question:</strong> <?php echo $analysisResult['avg_time']; ?> seconds</p>
                    </div>
                <?php elseif ($analysis_exam_id): ?>
                    <div class="alert alert-warning">No analysis data available for this exam.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>