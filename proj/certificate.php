<?php
// ATP - certificate.php
// Displays the certificate of completion for a course.
// Expects GET parameters: course_id and student_id

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'dbh.inc.php';

$courseID = $_GET['course_id'];
$studentID = $_GET['student_id'];

// Fetch Student Name
$sql = "SELECT Name FROM Students WHERE ID = :student_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['student_id' => $studentID]);
$student = $stmt->fetch();
$student_name = $student ? $student['Name'] : '';

// Fetch Course Name and Instructor ID
$sql = "SELECT Name, InstructorID FROM Courses WHERE ID = :course_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID]);
$course = $stmt->fetch();
$course_name = $course ? $course['Name'] : '';
$instructor_id = $course ? $course['InstructorID'] : '';

// Fetch Instructor Name
$sql = "SELECT Name FROM Instructors WHERE ID = :instructor_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['instructor_id' => $instructor_id]);
$instructor = $stmt->fetch();
$instructor_name = $instructor ? $instructor['Name'] : '';

// Fetch End Date from CourseRegistration
$sql = "SELECT EndDate FROM CourseRegistration WHERE CourseID = :course_id AND StudentID = :student_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['course_id' => $courseID, 'student_id' => $studentID]);
$registration = $stmt->fetch();
$end_date = $registration ? $registration['EndDate'] : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ATP - certificate.php -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Completion Certificate</title>
    <!-- Bootstrap 5 CSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* New Color Scheme for Certificate */
        body {
            background-color: #f0f4f8;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
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
            border: 20px solid #2c7873;
            padding: 40px;
            position: relative;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.15);
            border-radius: 10px;
        }

        .certificate:before {
            content: '';
            position: absolute;
            top: 10px;
            left: 10px;
            right: 10px;
            bottom: 10px;
            border: 2px solid #2c7873;
        }

        .content {
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .logo {
            font-size: 32px;
            font-weight: bold;
            color: #2c7873;
            margin-bottom: 20px;
        }

        h1 {
            color: #2c7873;
            font-size: 38px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .title {
            font-size: 18px;
            color: #555;
            margin-bottom: 20px;
        }

        .student-name {
            font-size: 34px;
            font-weight: bold;
            color: #2c7873;
            margin: 20px 0;
            position: relative;
            display: inline-block;
        }

        .student-name:after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: -8px;
            height: 2px;
            background-color: #2c7873;
        }

        .certificate-text {
            font-size: 18px;
            color: #444;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .course-name {
            font-size: 22px;
            font-weight: bold;
            color: #222;
            margin-top: 10px;
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
            margin-bottom: 8px;
            font-style: italic;
            color: #555;
        }

        .instructor-name {
            font-size: 16px;
            color: #444;
        }

        .date {
            font-size: 16px;
            color: #444;
        }

        .certificate-footer {
            margin-top: 30px;
            font-size: 14px;
            color: #777;
            text-align: center;
        }

        .seal {
            position: absolute;
            bottom: 70px;
            right: 60px;
            width: 100px;
            height: 100px;
            border: 2px solid #2c7873;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #2c7873;
            font-size: 24px;
            transform: rotate(-15deg);
        }
    </style>
</head>

<body>
    <div class="certificate-container">
        <div class="certificate">
            <div class="content">
                <div class="logo">IIT Guwahati</div>
                <h1>CERTIFICATE OF COMPLETION</h1>
                <div class="title">This certificate is awarded to</div>
                <div class="student-name"><?php echo $student_name; ?></div>
                <div class="certificate-text">
                    For successfully completing the course
                    <div class="course-name"><?php echo $course_name; ?></div>
                    with a demonstration of dedication and skill.
                </div>
                <div class="details">
                    <div class="detail-left">
                        <div class="signature">Instructor Signature</div>
                        <div class="instructor-name"><?php echo $instructor_name; ?></div>
                    </div>
                    <div class="detail-right">
                        <div class="signature">Director Signature</div>
                        <div class="instructor-name">IIT Guwahati Director</div>
                        <div class="date"><?php echo $end_date; ?></div>
                    </div>
                </div>
                <div class="certificate-footer">
                    This certificate verifies that the above-mentioned student has completed all requirements for the course according to the standards of the academy.
                </div>
                <div class="seal">SEAL</div>
            </div>
        </div>
    </div>
</body>

</html>