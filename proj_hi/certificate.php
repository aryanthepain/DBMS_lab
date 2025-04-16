<?php

/**
 * File: certificate.php
 * Author: Aryan Gupta
 *
 * This page generates the Course Completion Certificate for a student.
 * It fetches student, course, instructor, and completion data from the database.
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'dbh.inc.php'; // $pdo is available here

// Retrieve GET parameters and validate
$courseID  = isset($_GET['course_id']) ? trim($_GET['course_id']) : '';
$studentID = isset($_GET['student_id']) ? trim($_GET['student_id']) : '';

if (empty($courseID) || empty($studentID)) {
    echo "<script>alert('Missing course or student information.'); window.location.href='student_dashboard.php';</script>";
    exit();
}

// Fetch Student Name
$student_name = "";
$stmt = $pdo->prepare("SELECT Name FROM students WHERE ID = ?");
$stmt->execute([$studentID]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $student_name = $row['Name'];
}
$stmt = null;

// Fetch Course Name and Instructor ID
$course_name = "";
$instructor_id = "";
$stmt = $pdo->prepare("SELECT Name, InstructorID FROM courses WHERE ID = ?");
$stmt->execute([$courseID]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $course_name = $row['Name'];
    $instructor_id = $row['InstructorID'];
}
$stmt = null;

// Fetch Instructor Name
$instructor_name = "";
$stmt = $pdo->prepare("SELECT Name FROM instructors WHERE ID = ?");
$stmt->execute([$instructor_id]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $instructor_name = $row['Name'];
}
$stmt = null;

// Fetch End Date from CourseRegistration
$end_date = "";
$stmt = $pdo->prepare("SELECT EndDate FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
$stmt->execute([$courseID, $studentID]);
if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $end_date = $row['EndDate'];
}
$stmt = null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Completion Certificate - eLearn</title>
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

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .certificate-container {
            width: 800px;
            position: relative;
            padding: 20px;
        }

        .certificate {
            background-color: var(--bg-white);
            border: 20px solid var(--primary);
            padding: 40px;
            position: relative;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
        }

        .certificate:before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 2px solid var(--primary);
        }

        .content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            height: 80px;
            margin-bottom: 20px;
            background-color: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-weight: bold;
            font-size: 24px;
        }

        h1 {
            color: var(--primary);
            font-size: 40px;
            margin: 0;
            font-weight: 600;
        }

        .title {
            font-size: 18px;
            color: #555;
            margin: 5px 0 20px;
        }

        .certificate-title {
            font-size: 28px;
            margin-bottom: 30px;
            color: #222;
        }

        .student-name {
            font-size: 32px;
            font-weight: bold;
            color: var(--primary);
            margin: 20px 0;
            position: relative;
            display: inline-block;
        }

        .student-name:after {
            content: '';
            position: absolute;
            height: 2px;
            width: 100%;
            bottom: -10px;
            left: 0;
            background-color: var(--primary);
        }

        .certificate-text {
            font-size: 18px;
            line-height: 1.6;
            color: #444;
            margin: 20px 0 30px;
        }

        .course-name {
            font-weight: bold;
            font-size: 22px;
            color: #222;
        }

        .details {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
        }

        .detail-left,
        .detail-right {
            text-align: center;
            width: 45%;
        }

        .signature {
            height: 60px;
            margin: 0 auto 10px;
            display: flex;
            align-items: flex-end;
            justify-content: center;
            overflow: hidden;
            font-style: italic;
            color: #555;
        }

        .date,
        .instructor-name {
            font-size: 16px;
            color: #444;
        }

        .certificate-footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }

        .certificate-id {
            margin-top: 10px;
            font-size: 12px;
            color: #999;
        }

        .seal {
            position: absolute;
            bottom: 70px;
            right: 60px;
            width: 100px;
            height: 100px;
            background-image: radial-gradient(circle, transparent 50%, var(--primary-light)22 50%, var(--primary-light)22 52%, transparent 52%);
            background-size: 20px 20px;
            border-radius: 50%;
            border: 2px solid var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: var(--primary);
            font-size: 24px;
            transform: rotate(-15deg);
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="certificate">
            <div class="content">
                <div class="header">
                    <div class="logo">IIT Guwahati</div>
                    <h1>CERTIFICATE OF COMPLETION</h1>
                    <div class="title">This certificate is awarded to</div>
                </div>

                <div class="student-name"><?php echo htmlspecialchars($student_name); ?></div>

                <div class="certificate-text">
                    For successfully completing the course<br>
                    <span class="course-name"><?php echo htmlspecialchars($course_name); ?></span><br>
                    with a demonstration of dedication and skill.
                </div>

                <div class="details">
                    <div class="detail-left">
                        <div class="signature">Instructor Signature</div>
                        <div class="instructor-name"><?php echo htmlspecialchars($instructor_name); ?></div>
                        <div class="title">Instructor</div>
                    </div>
                    <div class="detail-right">
                        <div class="signature">Director Signature</div>
                        <div class="instructor-name">IIT Guwahati Director</div>
                        <div class="date"><?php echo !empty($end_date) ? date("F j, Y, g:i A", strtotime($end_date)) : ''; ?></div>
                    </div>
                </div>

                <div class="certificate-footer">
                    This certificate verifies that the above-mentioned student has completed all requirements
                    for the specified course in accordance with the standards set by the academy.
                </div>

                <div class="certificate-id">Certificate ID</div>
                <div class="seal">SEAL</div>
            </div>
        </div>
    </div>
</body>

</html>