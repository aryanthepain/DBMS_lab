<!-- view_course.php -->
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
$courseID = $_GET['course_id'];
$studentID = $_GET['student_id'];

$host = "localhost";
$user = "root";
$password = "";
$db = "DA215_Project2";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



$courseID = isset($_GET['course_id']) ? $_GET['course_id'] : '';
if (!$courseID) {
    echo "<script>alert('No course selected.'); window.location.href='student_dashboard.php';</script>";
    exit();
}


$status = '';
$statusStmt = $conn->prepare("SELECT StatusOfCompletion FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
$statusStmt->bind_param("ss", $courseID, $studentID);
$statusStmt->execute();
$statusResult = $statusStmt->get_result();
if ($statusRow = $statusResult->fetch_assoc()) {
    $status = $statusRow['StatusOfCompletion'];
}
$statusStmt->close();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['complete_course'])) {
    $currentTimestamp = date("Y-m-d H:i:s"); // Get current time in MySQL DATETIME format
    $updateStmt = $conn->prepare("UPDATE CourseRegistration SET StatusOfCompletion = 'Completed', EndDate = ? WHERE CourseID = ? AND StudentID = ?");
    $updateStmt->bind_param("sss", $currentTimestamp, $courseID, $studentID);
    $updateStmt->execute();
    $updateStmt->close();

    // Refresh page to show updated status
    header("Location: stud_view_course.php?course_id=$courseID&student_id=$studentID");
    exit();
}


// Fetch course content
$contentStmt = $conn->prepare("SELECT Name, Description FROM CourseContents WHERE CourseID = ?");
$contentStmt->bind_param("s", $courseID);
$contentStmt->execute();
$contentResult = $contentStmt->get_result();
$content = $contentResult->fetch_all(MYSQLI_ASSOC);
$contentStmt->close();

// Fetch announcements
$annStmt = $conn->prepare("SELECT Announcement FROM Announcements WHERE CourseID = ?");
$annStmt->bind_param("s", $courseID);
$annStmt->execute();
$annResult = $annStmt->get_result();
$announcements = $annResult->fetch_all(MYSQLI_ASSOC);
$annStmt->close();

// Fetch discussions
$disStmt = $conn->prepare("
    SELECT d.Discussion, s.Name AS StudentName, d.TimeStamp 
    FROM Discussions d
    JOIN Students s ON d.StudentID = s.ID
    WHERE d.CourseID = ?
    ORDER BY d.TimeStamp DESC
");
$disStmt->bind_param("s", $courseID);
$disStmt->execute();
$disResult = $disStmt->get_result();
$discussions = $disResult->fetch_all(MYSQLI_ASSOC);
$disStmt->close();


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['discussion'])) {
    $discussion = trim($_POST['discussion']);
    $stmt = $conn->prepare("INSERT INTO Discussions (CourseID, StudentID,Discussion) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $courseID, $studentID,$discussion);
    $stmt->execute();
    $stmt->close();
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Course Details</title>
    <style>
        .tab-container {
            background: #f8f9fa;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            padding: 20px;
            max-width: 800px;
            margin: 20px auto;
            font-family: 'Segoe UI', sans-serif;
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
        width: 100%;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .content-form input,
    .content-form textarea {
        width: 100%;
        padding: 12px;
        margin: 8px 0;
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
    .discussion-form {
    margin-top: 30px;
    display: flex;
    justify-content: center;
}

.discussion-form form {
    width: 100%;
    max-width: 500px;
    padding: 25px;
    background-color: #ffffff;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-left:28%;
}

.discussion-form label {
    font-weight: 600;
    font-size: 16px;
    color: #333;
}

.discussion-form textarea {
    height: 120px;
    resize: vertical;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 8px;
    font-family: inherit;
}

.discussion-form .btn-submit {
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

.discussion-form .btn-submit:hover {
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
<a href="student_dashboard.php" class="back-btn">← Back to Dashboard</a>
    <div class="tab-container">
        <div class="tabs">
            <button class="tab active" onclick="showTab(event, 'content')">Course Content</button>
            <button class="tab" onclick="showTab(event, 'announcements')">Announcements</button>
            <button class="tab" onclick="showTab(event, 'discussions')">Discussions</button>
        </div>

        <div class="tab-content" id="content-tab">
            <?php if (count($content) > 0): foreach ($content as $row): ?>
                <div class="card">
                    <h4><?php echo htmlspecialchars($row['Name']); ?></h4>
                    <p><?php echo nl2br(htmlspecialchars($row['Description'])); ?></p>
                </div>
            <?php endforeach; else: ?>
                <p>No course content available.</p>
            <?php endif; ?>
            <?php if (strtolower($status) !== 'completed'): ?>
            <form method="POST" style="text-align: center; margin-top: 20px;">
                <button type="submit" name="complete_course" class="btn">Mark Course as Completed</button>
            </form>

            <?php else: ?>
                <p style="text-align: center; margin-top: 20px; font-weight: bold; color: green;">
                    ✅ Course already marked as completed.
                </p>
                <div style="text-align: center; margin-top: 10px;">
                    <a href="certificate.php?student_id=<?php echo urlencode($studentID); ?>&course_id=<?php echo urlencode($courseID); ?>" 
                    class="btn" style="padding: 10px 20px; background-color: #0a5e84; color: #fff; text-decoration: none; border-radius: 5px;">
                        🎓 View Certificate
                    </a>
                </div>
            <?php endif; ?>


            <?php
                $endDate = '';
                $endStmt = $conn->prepare("SELECT EndDate FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
                $endStmt->bind_param("ss", $courseID, $studentID);
                $endStmt->execute();
                $endResult = $endStmt->get_result();
                if ($endRow = $endResult->fetch_assoc()) {
                    $endDate = $endRow['EndDate'];
                }
                $endStmt->close();

                if ($endDate) {
                    echo "<p style='text-align: center; color: #555;'>Completed on: " . date("F j, Y, g:i A", strtotime($endDate)) . "</p>";
                }
            ?>
        </div>

        <div class="tab-content hidden" id="announcements-tab">
            <?php if (count($announcements) > 0): foreach ($announcements as $row): ?>
                <div class="card">
                    <p><?php echo nl2br(htmlspecialchars($row['Announcement'])); ?></p>
                </div>
            <?php endforeach; else: ?>
                <p>No announcements yet.</p>
            <?php endif; ?>
            
        </div>

        <div class="tab-content hidden" id="discussions-tab">
            <?php if (count($discussions) > 0): foreach ($discussions as $row): ?>
                <div class="card">
                    <strong><?php echo htmlspecialchars($row['StudentName']); ?></strong>
                    <span style="float: right; font-size: 12px; color: gray;">
                        <?php echo date("M d, Y h:i A", strtotime($row['TimeStamp'])); ?>
                    </span>
                    <p style="margin-top: 8px;"><?php echo nl2br(htmlspecialchars($row['Discussion'])); ?></p>
                </div>
            <?php endforeach; else: ?>
                <p>No discussions yet.</p>
            <?php endif; ?>
            <button id="addDiscussionBtn" class="btn">Add Discussion</button>
        </div>
    </div>

    <div id="discussionForm" class="discussion-form" style="display: none;">
        <form action="" method="POST">
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
