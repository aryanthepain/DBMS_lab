<?php
// ATP - stud_view_course.php
// This page allows a student to view details of a selected course, see course content, announcements, and discussions.
// It also offers the ability to mark the course as completed or add a new discussion message.

session_start();
require_once 'dbh.inc.php';

$courseID = $_GET['course_id'] ?? '';
$studentID = $_GET['student_id'] ?? '';

if (!$courseID) {
    echo "<script>alert('No course selected.'); window.location.href='student_dashboard.php';</script>";
    exit();
}

// Get completion status for this course registration
$sql = "SELECT StatusOfCompletion FROM CourseRegistration WHERE CourseID = :course_id AND StudentID = :student_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID, 'student_id' => $studentID]);
$registration = $stmt->fetch();
$status = $registration ? $registration['StatusOfCompletion'] : null;

// Handle course completion submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_course'])) {
    $currentTimestamp = date("Y-m-d H:i:s");
    $sql = "UPDATE CourseRegistration SET StatusOfCompletion = 'Completed', EndDate = :end_date WHERE CourseID = :course_id AND StudentID = :student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['end_date' => $currentTimestamp, 'course_id' => $courseID, 'student_id' => $studentID]);
    header("Location: stud_view_course.php?course_id=$courseID&student_id=$studentID");
    exit();
}

// Fetch course content
$sql = "SELECT Name, Description FROM CourseContents WHERE CourseID = :course_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID]);
$content = $stmt->fetchAll();

// Fetch announcements
$sql = "SELECT Announcement FROM Announcements WHERE CourseID = :course_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID]);
$announcements = $stmt->fetchAll();

// Fetch discussions
$sql = "SELECT d.Discussion, s.Name AS StudentName, d.TimeStamp FROM Discussions d JOIN Students s ON d.StudentID = s.ID WHERE d.CourseID = :course_id ORDER BY d.TimeStamp DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID]);
$discussions = $stmt->fetchAll();

// Handle new discussion submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['discussion'])) {
    $discussion = trim($_POST['discussion']);
    $sql = "INSERT INTO Discussions (CourseID, StudentID, Discussion) VALUES (:course_id, :student_id, :discussion)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['course_id' => $courseID, 'student_id' => $studentID, 'discussion' => $discussion]);
}
?>
<!DOCTYPE html>
<html>

<head>
    <!-- ATP - stud_view_course.php -->
    <meta charset="UTF-8">
    <title>Course Details</title>
    <!-- Bootstrap 5 CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* New styling for course view */
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        .tab-container {
            background: #ffffff;
            border-radius: 10px;
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
            background-color: #d1e8e2;
            border: none;
            font-weight: bold;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            transition: background-color 0.3s;
        }

        .tab.active {
            background-color: #2c7873;
            color: #fff;
        }

        .tab-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 0 0 10px 10px;
            border: 1px solid #ccc;
        }

        .hidden {
            display: none;
        }

        .card {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-bottom: 10px;
            background-color: #fefefe;
        }

        .btn {
            padding: 10px 20px;
            background-color: #2c7873;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #23635b;
        }

        .content-form,
        .announcement-form,
        .discussion-form {
            margin: 20px auto;
            padding: 20px;
            background-color: #eef7f5;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
        }

        .content-form input,
        .content-form textarea,
        .announcement-form textarea,
        .discussion-form textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 16px;
        }
    </style>
</head>

<body>
    <a href="student_dashboard.php" class="btn" style="margin-bottom:20px;">← Back to Dashboard</a>
    <div class="tab-container">
        <div class="tabs">
            <button class="tab active" onclick="showTab(event, 'content')">Course Content</button>
            <button class="tab" onclick="showTab(event, 'announcements')">Announcements</button>
            <button class="tab" onclick="showTab(event, 'discussions')">Discussions</button>
        </div>
        <div class="tab-content" id="content-tab">
            <?php if (count($content) > 0): ?>
                <?php foreach ($content as $row): ?>
                    <div class="card">
                        <h4><?php echo htmlspecialchars($row['Name']); ?></h4>
                        <p><?php echo nl2br(htmlspecialchars($row['Description'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No course content available.</p>
            <?php endif; ?>
            <button id="addContentBtn" class="btn">Add Content</button>
        </div>
        <div class="tab-content hidden" id="announcements-tab">
            <?php if (count($announcements) > 0): ?>
                <?php foreach ($announcements as $row): ?>
                    <div class="card">
                        <p><?php echo nl2br(htmlspecialchars($row['Announcement'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No announcements yet.</p>
            <?php endif; ?>
            <button id="addAnnouncementBtn" class="btn">Add Announcement</button>
        </div>
        <div class="tab-content hidden" id="discussions-tab">
            <?php if (count($discussions) > 0): ?>
                <?php foreach ($discussions as $row): ?>
                    <div class="card">
                        <strong><?php echo htmlspecialchars($row['StudentName']); ?></strong>
                        <span style="float: right; font-size: 12px; color: gray;">
                            <?php echo date("M d, Y h:i A", strtotime($row['TimeStamp'])); ?>
                        </span>
                        <p><?php echo nl2br(htmlspecialchars($row['Discussion'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No discussions yet.</p>
            <?php endif; ?>
            <button id="addDiscussionBtn" class="btn">Add Discussion</button>
        </div>
    </div>
    <div id="contentForm" class="content-form" style="display: none;">
        <form action="" method="POST">
            <label for="name">Title</label>
            <input type="text" id="name" name="name" required>
            <label for="content">Description</label>
            <textarea id="content" name="content" required></textarea>
            <button type="submit" class="btn">Submit</button>
        </form>
    </div>
    <div id="announcementForm" class="announcement-form" style="display: none;">
        <form action="" method="POST">
            <label for="announcement">Announcement</label>
            <textarea id="announcement" name="announcement" required></textarea>
            <button type="submit" class="btn">Submit Announcement</button>
        </form>
    </div>
    <div id="discussionForm" class="discussion-form" style="display: none;">
        <form action="" method="POST">
            <label for="discussion">Discussion</label>
            <textarea id="discussion" name="discussion" required></textarea>
            <button type="submit" class="btn">Submit Discussion</button>
        </form>
    </div>
    <script>
        function showTab(event, tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            event.target.classList.add('active');
        }
        const addContentBtn = document.getElementById("addContentBtn");
        const contentForm = document.getElementById("contentForm");
        addContentBtn.addEventListener("click", function() {
            contentForm.style.display = (contentForm.style.display === "none") ? "block" : "none";
        });
        const addAnnouncementBtn = document.getElementById("addAnnouncementBtn");
        const announcementForm = document.getElementById("announcementForm");
        addAnnouncementBtn.addEventListener("click", function() {
            announcementForm.style.display = (announcementForm.style.display === "none") ? "block" : "none";
        });
        const addDiscussionBtn = document.getElementById("addDiscussionBtn");
        const discussionForm = document.getElementById("discussionForm");
        addDiscussionBtn.addEventListener("click", function() {
            discussionForm.style.display = (discussionForm.style.display === "none") ? "block" : "none";
        });
    </script>
</body>

</html>