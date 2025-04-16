<?php

/**
 * File: view_course.php
 * Author: Aryan Gupta
 *
 * Displays course details—including content, announcements, and discussions—
 * for a selected course. Instructors can add new course content or announcements.
 */

session_start();
require_once 'dbh.inc.php'; // This file creates a PDO connection stored in $pdo

// Get course ID from query string, redirect if not provided.
$courseID = isset($_GET['id']) ? trim($_GET['id']) : '';
if (!$courseID) {
    echo "<script>alert('No course selected.'); window.location.href='instructor_dashboard.php';</script>";
    exit();
}

// Fetch course content
$contentStmt = $pdo->prepare("SELECT Name, Description FROM CourseContents WHERE CourseID = ?");
$contentStmt->execute([$courseID]);
$courseContent = $contentStmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch announcements
$annStmt = $pdo->prepare("SELECT Announcement FROM Announcements WHERE CourseID = ?");
$annStmt->execute([$courseID]);
$announcements = $annStmt->fetchAll(PDO::FETCH_ASSOC);

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

// Handle adding new course content
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['name']) && isset($_POST['content'])) {
    $newContentTitle = trim($_POST['name']);
    $newContentText  = trim($_POST['content']);

    $stmt = $pdo->prepare("INSERT INTO CourseContents (CourseID, Name, Description) VALUES (?, ?, ?)");
    $stmt->execute([$courseID, $newContentTitle, $newContentText]);
    // Optionally, you could redirect or refresh to update the content list
    header("Location: view_course.php?id=" . urlencode($courseID));
    exit();
}

// Handle adding a new announcement
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['announcement'])) {
    $newAnnouncement = trim($_POST['announcement']);
    $stmt = $pdo->prepare("INSERT INTO Announcements (CourseID, Announcement) VALUES (?, ?)");
    $stmt->execute([$courseID, $newAnnouncement]);
    header("Location: view_course.php?id=" . urlencode($courseID));
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Course Details - eLearn</title>
    <style>
        /* Consistent colour scheme and styling */
        body {
            background: linear-gradient(135deg, #eef2f3 0%, #8e9eab 100%);
            font-family: 'Segoe UI', sans-serif;
            margin: 0;
            padding: 0;
        }

        .tab-container {
            background: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            padding: 20px;
            max-width: 800px;
            margin: 40px auto;
        }

        .tabs {
            display: flex;
            justify-content: space-around;
            margin-bottom: 20px;
        }

        .tab {
            flex: 1;
            padding: 12px;
            background-color: #e0e0e0;
            border: none;
            font-weight: bold;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            transition: background-color 0.3s;
        }

        .tab.active {
            background-color: #007bff;
            color: white;
        }

        .tab:hover {
            background-color: #d0d0d0;
        }

        .tab-content {
            background-color: white;
            padding: 20px;
            border-radius: 0 0 12px 12px;
            border: 1px solid #ddd;
        }

        .hidden {
            display: none;
        }

        .card {
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 10px;
            margin-bottom: 10px;
            background-color: #fefefe;
        }

        .btn {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .content-form {
            margin-top: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 20px auto;
        }

        .content-form input,
        .content-form textarea {
            width: 100%;
            padding: 12px;
            margin: 8px -13px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
        }

        .content-form textarea {
            height: 150px;
            resize: vertical;
        }

        .btn-submit {
            padding: 10px 20px;
            background-color: #008CBA;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn-submit:hover {
            background-color: #007B8F;
        }

        .announcement-form {
            margin-top: 30px;
            display: flex;
            justify-content: center;
        }

        .announcement-form form {
            width: 100%;
            max-width: 500px;
            padding: 25px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: auto;
            margin-bottom: 20px;
        }

        .announcement-form label {
            font-weight: 600;
            font-size: 16px;
            color: #333;
        }

        .announcement-form textarea {
            height: 120px;
            resize: vertical;
            padding: 12px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: inherit;
        }

        .announcement-form .btn-submit {
            align-self: center;
            padding: 10px 24px;
            font-size: 16px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .announcement-form .btn-submit:hover {
            background-color: #0056b3;
        }

        .back-btn {
            display: inline-block;
            margin: 20px;
            padding: 10px 18px;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            font-size: 15px;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <a href="instructor_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <div class="tab-container">
        <div class="tabs">
            <button class="tab active" onclick="showTab(event, 'content')">Course Content</button>
            <button class="tab" onclick="showTab(event, 'announcements')">Announcements</button>
            <button class="tab" onclick="showTab(event, 'discussions')">Discussions</button>
        </div>

        <div class="tab-content" id="content-tab">
            <?php if (count($courseContent) > 0): ?>
                <?php foreach ($courseContent as $row): ?>
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
                        <p style="margin-top: 8px;"><?php echo nl2br(htmlspecialchars($row['Discussion'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No discussions yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <div id="contentForm" class="content-form" style="display: none;">
        <form method="POST" action="">
            <label for="name">Title</label>
            <input type="text" id="name" name="name" required>
            <label for="content">Description</label>
            <textarea id="content" name="content" required></textarea>
            <button type="submit" class="btn-submit">Submit</button>
        </form>
    </div>

    <div id="announcementForm" class="announcement-form" style="display: none;">
        <form method="POST" action="">
            <label for="announcement">Announcement</label>
            <textarea id="announcement" name="announcement" required></textarea>
            <button type="submit" class="btn-submit">Submit Announcement</button>
        </form>
    </div>

    <script>
        function showTab(event, tabName) {
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            document.querySelectorAll('.tab').forEach(el => el.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            event.target.classList.add('active');
        }

        // Toggle content form
        const addContentBtn = document.getElementById("addContentBtn");
        const contentForm = document.getElementById("contentForm");
        addContentBtn.addEventListener("click", function() {
            contentForm.style.display = contentForm.style.display === "none" ? "block" : "none";
        });

        // Toggle announcement form
        const addAnnouncementBtn = document.getElementById("addAnnouncementBtn");
        const announcementForm = document.getElementById("announcementForm");
        addAnnouncementBtn.addEventListener("click", function() {
            announcementForm.style.display = announcementForm.style.display === "none" ? "block" : "none";
        });
    </script>
</body>

</html>