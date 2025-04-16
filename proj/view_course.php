<?php
// ATP - view_course.php
// Shows detailed view of a selected course for a student, including course content, announcements, and discussions.
// Also enables marking the course as complete and adding discussion comments.

session_start();
require_once 'dbh.inc.php';

$courseID = $_GET['course_id'] ?? '';
$studentID = $_GET['student_id'] ?? '';

if (!$courseID || !$studentID) {
    echo "<script>alert('Course or student not specified.'); window.location.href='student_dashboard.php';</script>";
    exit();
}

// Handle course completion submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complete_course'])) {
    $currentTimestamp = date("Y-m-d H:i:s");
    $sql = "UPDATE CourseRegistration SET StatusOfCompletion = 'Completed', EndDate = :end_date 
            WHERE CourseID = :course_id AND StudentID = :student_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['end_date' => $currentTimestamp, 'course_id' => $courseID, 'student_id' => $studentID]);
    header("Location: view_course.php?course_id=" . urlencode($courseID) . "&student_id=" . urlencode($studentID));
    exit();
}

// Handle adding a discussion comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['discussion'])) {
    $discussion = trim($_POST['discussion']);
    $sql = "INSERT INTO Discussions (CourseID, StudentID, Discussion) VALUES (:course_id, :student_id, :discussion)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['course_id' => $courseID, 'student_id' => $studentID, 'discussion' => $discussion]);
}

// Fetch course content
$sql = "SELECT Name, Description FROM CourseContents WHERE CourseID = :course_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID]);
$contents = $stmt->fetchAll();

// Fetch announcements
$sql = "SELECT Announcement FROM Announcements WHERE CourseID = :course_id ORDER BY ID DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID]);
$announcements = $stmt->fetchAll();

// Fetch discussions
$sql = "SELECT d.Discussion, s.Name AS StudentName, d.TimeStamp FROM Discussions d 
        JOIN Students s ON d.StudentID = s.ID 
        WHERE d.CourseID = :course_id ORDER BY d.TimeStamp DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID]);
$discussions = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ATP - view_course.php -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <!-- Bootstrap 5 CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Remixed course view styles */
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            padding: 20px;
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        .tab-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-top: 20px;
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
            border: 1px solid #ddd;
            border-top: none;
            border-radius: 0 0 8px 8px;
        }

        .hidden {
            display: none;
        }

        .card {
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }

        .btn {
            background-color: #2c7873;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #23635b;
        }

        .form-section {
            background-color: #eef7f5;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <a href="student_dashboard.php" class="btn mb-3">← Back to Dashboard</a>
    <div class="container">
        <div class="tab-container">
            <div class="tabs">
                <button class="tab active" onclick="showTab(event, 'content')">Course Content</button>
                <button class="tab" onclick="showTab(event, 'announcements')">Announcements</button>
                <button class="tab" onclick="showTab(event, 'discussions')">Discussions</button>
            </div>
            <div id="content-tab" class="tab-content">
                <?php if (count($contents) > 0): ?>
                    <?php foreach ($contents as $c): ?>
                        <div class="card">
                            <h4><?php echo htmlspecialchars($c['Name']); ?></h4>
                            <p><?php echo nl2br(htmlspecialchars($c['Description'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No course content available.</p>
                <?php endif; ?>
                <?php if (strtolower($status) !== 'completed'): ?>
                    <form method="POST">
                        <button type="submit" name="complete_course" class="btn">Mark as Completed</button>
                    </form>
                <?php else: ?>
                    <p class="text-success">Course marked as Completed.</p>
                    <a href="certificate.php?course_id=<?php echo urlencode($courseID); ?>&student_id=<?php echo urlencode($studentID); ?>" class="btn">View Certificate</a>
                <?php endif; ?>
            </div>
            <div id="announcements-tab" class="tab-content hidden">
                <?php if (count($announcements) > 0): ?>
                    <?php foreach ($announcements as $a): ?>
                        <div class="card">
                            <p><?php echo nl2br(htmlspecialchars($a['Announcement'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No announcements yet.</p>
                <?php endif; ?>
            </div>
            <div id="discussions-tab" class="tab-content hidden">
                <?php if (count($discussions) > 0): ?>
                    <?php foreach ($discussions as $d): ?>
                        <div class="card">
                            <strong><?php echo htmlspecialchars($d['StudentName']); ?></strong>
                            <small class="text-muted float-end"><?php echo date("M d, Y h:i A", strtotime($d['TimeStamp'])); ?></small>
                            <p><?php echo nl2br(htmlspecialchars($d['Discussion'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No discussions yet.</p>
                <?php endif; ?>
            </div>
        </div>
        <!-- Discussion Form -->
        <div class="form-section" id="discussionForm" style="display: none;">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="discussion" class="form-label">Add Discussion</label>
                    <textarea id="discussion" name="discussion" class="form-control" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn">Submit Discussion</button>
            </form>
        </div>
    </div>
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showTab(event, tabName) {
            const tabs = document.querySelectorAll('.tab-content');
            tabs.forEach(tab => tab.classList.add('hidden'));
            const tabButtons = document.querySelectorAll('.tab');
            tabButtons.forEach(btn => btn.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            event.target.classList.add('active');
        }
        const discussionTab = document.getElementById('discussions-tab');
        // Toggle discussion form when clicking "Add Discussion" (could add a button below discussions)
        const addDiscussionBtn = document.createElement("button");
        addDiscussionBtn.textContent = "Add Discussion";
        addDiscussionBtn.className = "btn mt-3";
        addDiscussionBtn.onclick = function() {
            const form = document.getElementById("discussionForm");
            form.style.display = form.style.display === "none" ? "block" : "none";
        };
        discussionTab.parentNode.insertBefore(addDiscussionBtn, discussionTab.nextSibling);
    </script>
</body>

</html>