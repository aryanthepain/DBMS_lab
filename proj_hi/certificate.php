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

// Fetch Student Name
$student_name = "";
$student_query = $conn->prepare("SELECT Name FROM students WHERE ID = ?");
$student_query->bind_param("s", $studentID);
$student_query->execute();
$student_result = $student_query->get_result();
if ($row = $student_result->fetch_assoc()) {
    $student_name = $row['Name'];
}
$student_query->close();

// Fetch Course Name and Instructor ID
$course_name = "";
$instructor_id = "";
$course_query = $conn->prepare("SELECT Name, InstructorID FROM courses WHERE ID = ?");
$course_query->bind_param("s", $courseID);
$course_query->execute();
$course_result = $course_query->get_result();
if ($row = $course_result->fetch_assoc()) {
    $course_name = $row['Name'];
    $instructor_id = $row['InstructorID'];
}
$course_query->close();

// Fetch Instructor Name
$instructor_name = "";
$instructor_query = $conn->prepare("SELECT Name FROM instructors WHERE ID = ?");
$instructor_query->bind_param("s", $instructor_id);
$instructor_query->execute();
$instructor_result = $instructor_query->get_result();
if ($row = $instructor_result->fetch_assoc()) {
    $instructor_name = $row['Name'];
}
$instructor_query->close();

// Fetch End Date
$end_date = "";
$end_query = $conn->prepare("SELECT EndDate FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
$end_query->bind_param("ss", $courseID, $studentID);
$end_query->execute();
$end_result = $end_query->get_result();
if ($row = $end_result->fetch_assoc()) {
    $end_date = $row['EndDate'];
}
$end_query->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Completion Certificate</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f9f9f9;
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
            background-color: #fff;
            border: 20px solid #0a5e84;
            padding: 40px;
            position: relative;
            box-shadow: 0 0 25px rgba(0,0,0,0.15);
        }
        .certificate:before {
            content: '';
            position: absolute;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            border: 2px solid #0a5e84;
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
        }
        h1 {
            color: #0a5e84;
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
            color: #0a5e84;
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
            background-color: #0a5e84;
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
        .detail-left, .detail-right {
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
        .date, .instructor-name {
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
            background-image: radial-gradient(circle, transparent 50%, #0a5e8422 50%, #0a5e8422 52%, transparent 52%);
            background-size: 20px 20px;
            border-radius: 50%;
            border: 2px solid #0a5e84;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #0a5e84;
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
                
                <div class="student-name"><?php echo $student_name;?></div>
                
                <div class="certificate-text">
                    For successfully completing the course
                    <div class="course-name"><?php echo $course_name;?></div>
                    with a demonstration of dedication and skill.
                </div>
                
                <div class="details">
                    <div class="detail-left">
                        <div class="signature">Instructor Signature</div>
                        <div class="instructor-name"><?php echo $instructor_name;?></div>
                        <div class="title">Instructor</div>
                    </div>
                    <div class="detail-right">
                        <div class="signature">Director Signature</div>
                        <div class="instructor-name">IIT Guwahati Director</div>
                        <div class="date"><?php echo $end_date;?></div>
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