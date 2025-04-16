<?php

/**
 * File: stud_view_course.php
 * Author: Aryan Gupta
 *
 * This page displays details of a selected course for a student.
 * It shows course content, announcements, and discussions.
 * Students can mark the course as completed or add a discussion.
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'dbh.inc.php'; // Ensure $pdo is available

// Get course and student IDs from GET parameters
$courseID  = isset($_GET['course_id']) ? trim($_GET['course_id']) : '';
$studentID = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';

if (!$courseID) {
    echo "<script>alert('No course selected.'); window.location.href='student_dashboard.php';</script>";
    exit();
}

// Fetch current course status for this student (if any)
$status = '';
$statusStmt = $pdo->prepare("SELECT StatusOfCompletion FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
$statusStmt->execute([$courseID, $studentID]);
$statusRow = $statusStmt->fetch(PDO::FETCH_ASSOC);
if ($statusRow) {
    $status = $statusRow['StatusOfCompletion'];
}
$statusStmt = null;

// Process marking course complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_course'])) {
    $currentTimestamp = date("Y-m-d H:i:s"); // MySQL DATETIME format
    $updateStmt = $pdo->prepare("UPDATE CourseRegistration SET StatusOfCompletion = 'Completed', EndDate = ? WHERE CourseID = ? AND StudentID = ?");
    $updateStmt->execute([$currentTimestamp, $courseID, $studentID]);
    $updateStmt = null;
    header("Location: stud_view_course.php?course_id=" . urlencode($courseID) . "&student_id=" . urlencode($studentID));
    exit();
}

// Fetch course content
$contentStmt = $pdo->prepare("SELECT Name, Description FROM CourseContents WHERE CourseID = ?");
$contentStmt->execute([$courseID]);
$content = $contentStmt->fetchAll(PDO::FETCH_ASSOC);
$contentStmt = null;

// Fetch announcements
$annStmt = $pdo->prepare("SELECT Announcement FROM Announcements WHERE CourseID = ?");
$annStmt->execute([$courseID]);
$announcements = $annStmt->fetchAll(PDO::FETCH_ASSOC);
$annStmt = null;

// Fetch discussions
$disStmt = $pdo->prepare("
    SELECT d.Discussion, s.Name AS StudentName, d.TimeStamp 
    FROM Discussions d
    JOIN Students s ON d.StudentID = s.ID
    WHERE d.CourseID = ?
    ORDER BY d.TimeStamp DESC
");
$disStmt->execute([$courseID]);
$discussions = $disStmt->fetchAll(PDO::FETCH_ASSOC);
$disStmt = null;

// Handle adding a new discussion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['discussion'])) {
    $discussionText = trim($_POST['discussion']);
    $insStmt = $pdo->prepare("INSERT INTO Discussions (CourseID, StudentID, Discussion) VALUES (?, ?, ?)");
    $insStmt->execute([$courseID, $studentID, $discussionText]);
    $insStmt = null;
    // Refresh to show updated discussions
    header("Location: stud_view_course.php?course_id=" . urlencode($courseID) . "&student_id=" . urlencode($studentID));
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Course Details - eLearn</title>
    <style>
        :root {
            --primary: #008080;
            /* Teal */
            --primary-light: #66c2c2;
            --dark: #0a3d62;
            --text-dark: #0a3d62;
            --text-light: #778899;
            --bg-light: #e6f2f0;
            --bg-white: #ffffff;
            --radius: 8px;
        }

        /* Global Reset */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            background: var(--bg-light);
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
            padding: 20px;
        }

        .back-btn {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 18px;
            background-color: var(--primary);
            color: var(--bg-white);
            text-decoration: none;
            font-size: 15px;
            border-radius: var(--radius);
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: var(--primary-light);
        }

        .tab-container {
            background: var(--bg-white);
            border-radius: var(--radius);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
        }

        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .tab {
            flex: 1;
            padding: 12px;
            background-color: #d0dce0;
            border: none;
            font-weight: bold;
            cursor: pointer;
            border-radius: var(--radius) var(--radius) 0 0;
            transition: background-color 0.3s;
        }

        .tab.active {
            background-color: var(--primary);
            color: var(--bg-white);
        }

        .tab:hover {
            background-color: #b0bbc2;
        }

        .tab-content {
            background-color: var(--bg-white);
            padding: 20px;
            border-radius: 0 0 var(--radius) var(--radius);
            border: 1px solid #ddd;
        }

        .hidden {
            display: none;
        }

        .card {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: var(--radius);
            margin-bottom: 10px;
            background-color: var(--bg-white);
        }

        .btn {
            padding: 10px 20px;
            background-color: var(--primary);
            color: var(--bg-white);
            border: none;
            cursor: pointer;
            border-radius: var(--radius);
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: var(--primary-light);
        }

        .content-form,
        .discussion-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f5f7fa;
            border-radius: var(--radius);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .content-form input,
        .content-form textarea,
        .discussion-form textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: var(--radius);
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .content-form textarea,
        .discussion-form textarea {
            height: 150px;
            resize: vertical;
        }

        .btn-submit {
            padding: 10px 20px;
            background-color: #008CBA;
            color: var(--bg-white);
            border: none;
            cursor: pointer;
            border-radius: var(--radius);
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #007B8F;
        }

        /* Additional spacing for certificate link */
        .certificate-link {
            text-align: center;
            margin-top: 10px;
        }
    </style>
</head>

<body>
    <a href="student_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <div class="tab-container">
        <div class="tabs">
            <button class="tab active" onclick="showTab(event, 'content')">Course Content</button>
            <button class="tab" onclick="showTab(event, 'announcements')">Announcements</button>
            <button class="tab" onclick="showTab(event, 'discussions')">Discussions</button>
        </div>
        <div class="tab-content" id="content-tab">
            <?php if (!empty($content)): ?>
                <?php foreach ($content as $row): ?>
                    <div class="card" style="display: flex; gap: 12px; align-items: flex-start;">
                        <i class="fas fa-book-open" style="font-size: 24px; color: var(--primary); margin-top: 4px;"></i>
                        <div>
                            <h4><?php echo htmlspecialchars($row['Name']); ?></h4>
                            <p><?php echo nl2br(htmlspecialchars($row['Description'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No course content available.</p>
            <?php endif; ?>
            <?php if (strtolower($status) !== 'completed'): ?>
                <form method="POST" style="text-align: center; margin-top: 20px;">
                    <button type="submit" name="complete_course" class="btn">
                        <i class="fas fa-check"></i> Mark Course as Completed
                    </button>
                </form>
            <?php else: ?>
                <p style="text-align: center; margin-top: 20px; font-weight: bold; color: green;">
                    ✅ Course already marked as completed.
                </p>
                <div class="certificate-link">
                    <a href="certificate.php?student_id=<?php echo urlencode($studentID); ?>&course_id=<?php echo urlencode($courseID); ?>"
                        class="btn" style="background-color: #0a5e84;">
                        <i class="fas fa-graduation-cap"></i> View Certificate
                    </a>
                </div>
            <?php endif; ?>
            <?php
            // Fetch completion EndDate
            $endStmt = $pdo->prepare("SELECT EndDate FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
            $endStmt->execute([$courseID, $studentID]);
            $endRow = $endStmt->fetch(PDO::FETCH_ASSOC);
            if ($endRow && !empty($endRow['EndDate'])) {
                echo "<p style='text-align: center; color: #555;'>Completed on: " . date("F j, Y, g:i A", strtotime($endRow['EndDate'])) . "</p>";
            }
            $endStmt = null;
            ?>
        </div>
        <div class="tab-content hidden" id="announcements-tab">
            <?php if (!empty($announcements)): ?>
                <?php foreach ($announcements as $row): ?>
                    <div class="card">
                        <p><?php echo nl2br(htmlspecialchars($row['Announcement'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No announcements yet.</p>
            <?php endif; ?>
        </div>
        <div class="tab-content hidden" id="discussions-tab">
            <?php if (!empty($discussions)): ?>
                <?php foreach ($discussions as $row): ?>
                    <div class="card">
                        <strong><?php echo htmlspecialchars($row['StudentName']); ?></strong>
                        <span style="float: right; font-size: 12px; color: gray;">
                            <?php echo date("M d, Y h:i A", strtotime($row['TimeStamp'])); ?>
                        </span>
                        <p style="margin-top: 8px;"><?php echo nl2br(htmlspecialchars($row['Discussion'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No discussions yet.</p>
            <?php endif; ?>
            <button id="addDiscussionBtn" class="btn">Add Discussion</button>
        </div>
    </div>
    <div id="discussionForm" class="discussion-form" style="display: none;">
        <form method="POST" action="">
            <label for="discussion">Discussion</label>
            <textarea id="discussion" name="discussion" required></textarea>
            <button type="submit" class="btn-submit">Submit Discussion</button>
        </form>
    </div>
    <script>
        function showTab(event, tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            event.target.classList.add('active');
        }
        // Toggle discussion form
        const addDiscussionBtn = document.getElementById("addDiscussionBtn");
        const discussionForm = document.getElementById("discussionForm");
        addDiscussionBtn.addEventListener("click", function() {
            discussionForm.style.display = discussionForm.style.display === "none" ? "block" : "none";
        });
    </script>
</body>

</html>