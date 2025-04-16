<?php

/**
 * File: student_dashboard.php
 * Author: Aryan Gupta
 *
 * Student dashboard for eLearn using a PDO connection.
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'dbh.inc.php'; // This file creates a PDO connection stored in $pdo

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
  header("Location: index.php");
  exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details
$sqlStudent = "SELECT * FROM Students WHERE ID = ?";
$stmtStudent = $pdo->prepare($sqlStudent);
$stmtStudent->execute([$student_id]);
$studentData = $stmtStudent->fetch(PDO::FETCH_ASSOC);
$student_name = $studentData ? $studentData['Name'] : "Student";

// Fetch all available courses
$sqlCourses = "SELECT * FROM Courses";
$stmtCourses = $pdo->query($sqlCourses);
$courses = $stmtCourses->fetchAll(PDO::FETCH_ASSOC);

// Fetch registered courses for this student
$sqlReg = "
    SELECT 
      cr.CourseID,
      c.Name AS CourseName,
      c.InstructorID,
      cr.DateOfRegistration,
      cr.StatusOfCompletion,
      i.Name AS InstructorName
    FROM CourseRegistration cr
    JOIN Courses c ON cr.CourseID = c.ID
    JOIN Instructors i ON c.InstructorID = i.ID
    WHERE cr.StudentID = ?
";
$stmtReg = $pdo->prepare($sqlReg);
$stmtReg->execute([$student_id]);
$registeredCourses = $stmtReg->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard - eLearn</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --primary: #4361ee;
      --primary-light: #4895ef;
      --dark: #222831;
      --text-dark: #333;
      --text-light: #717171;
      --bg-light: #f9fafb;
      --bg-white: #ffffff;
      --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
      --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
      --green: #10b981;
      --blue: #3b82f6;
      --orange: #f59e0b;
      --red: #ef4444;
      --radius: 8px;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: var(--bg-light);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text-dark);
      line-height: 1.6;
    }

    .container {
      max-width: 1400px;
      margin: 0 auto;
      padding: 0 20px;
    }

    /* Header/Navigation */
    .header {
      background-color: var(--bg-white);
      box-shadow: var(--shadow);
      position: sticky;
      top: 0;
      z-index: 100;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0 20px;
      height: 70px;
    }

    .logo {
      font-size: 24px;
      font-weight: 700;
      color: var(--primary);
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .logo i {
      font-size: 28px;
    }

    .nav-menu {
      display: flex;
      align-items: center;
      gap: 30px;
    }

    .nav-item {
      list-style: none;
    }

    .nav-link {
      text-decoration: none;
      color: var(--text-dark);
      font-weight: 500;
      font-size: 16px;
      position: relative;
      padding: 25px 0;
      transition: color 0.3s;
    }

    .nav-link:hover,
    .nav-link.active {
      color: var(--primary);
    }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 3px;
      background-color: var(--primary);
    }

    .user-menu {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .user-info {
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .user-avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: var(--primary-light);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-weight: bold;
      font-size: 16px;
    }

    .user-name {
      font-weight: 500;
    }

    .logout-btn {
      background-color: transparent;
      border: 1px solid #e2e8f0;
      border-radius: var(--radius);
      padding: 8px 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 8px;
      transition: all 0.3s;
      color: var(--text-dark);
      font-weight: 500;
    }

    .logout-btn:hover {
      background-color: #f8f9fa;
      color: var(--red);
      border-color: var(--red);
    }

    /* Main Content */
    .main-content {
      padding: 30px 0;
      min-height: calc(100vh - 70px);
      display: none;
    }

    .main-content.active {
      display: block;
    }

    .page-header {
      margin-bottom: 30px;
    }

    .page-title {
      font-size: 26px;
      font-weight: 700;
      color: var(--dark);
      margin-bottom: 10px;
    }

    .page-subtitle {
      color: var(--text-light);
      font-size: 16px;
    }

    /* Course Cards */
    .course-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 25px;
    }

    .course-card {
      background: var(--bg-white);
      border-radius: var(--radius);
      overflow: hidden;
      box-shadow: var(--shadow);
      transition: transform 0.3s, box-shadow 0.3s;
      cursor: pointer;
      position: relative;
    }

    .course-card:hover {
      transform: translateY(-5px);
      box-shadow: var(--shadow-lg);
    }

    .course-banner {
      height: 140px;
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      position: relative;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 60px;
    }

    .course-category {
      position: absolute;
      top: 15px;
      right: 15px;
      background-color: rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      color: white;
      padding: 5px 10px;
      border-radius: 50px;
      font-size: 12px;
      font-weight: 500;
    }

    .course-content {
      padding: 25px;
    }

    .course-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 10px;
      color: var(--dark);
      line-height: 1.4;
    }

    .course-meta {
      display: flex;
      align-items: center;
      gap: 15px;
      margin-bottom: 15px;
    }

    .course-instructor {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: var(--text-light);
    }

    .course-credits {
      display: flex;
      align-items: center;
      gap: 8px;
      font-size: 14px;
      color: var(--text-light);
    }

    .course-description {
      font-size: 14px;
      color: var(--text-light);
      margin-bottom: 20px;
      -webkit-box-orient: vertical;
      overflow: hidden;
      line-height: 1.5;
    }

    .course-footer {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .view-details-btn {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 8px 16px;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: var(--radius);
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s;
      text-decoration: none;
      font-size: 14px;
    }

    .view-details-btn:hover {
      background-color: var(--primary-light);
    }

    .course-date {
      font-size: 14px;
      color: var(--text-light);
    }

    /* Registered Courses Table */
    .course-table-container {
      background: var(--bg-white);
      border-radius: var(--radius);
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .course-table {
      width: 100%;
      border-collapse: collapse;
    }

    .course-table th,
    .course-table td {
      padding: 16px 20px;
      text-align: left;
      border-bottom: 1px solid #f1f5f9;
    }

    .course-table th {
      background-color: #f8fafc;
      font-weight: 600;
      color: var(--text-dark);
      font-size: 15px;
    }

    .course-table tr:last-child td {
      border-bottom: none;
    }

    .course-table tr:hover td {
      background-color: #f9fafb;
    }

    .course-code {
      color: var(--primary);
      font-weight: 600;
    }

    .course-name {
      font-weight: 500;
    }

    .status-badge {
      display: inline-flex;
      align-items: center;
      gap: 5px;
      padding: 6px 12px;
      border-radius: 50px;
      font-size: 13px;
      font-weight: 500;
    }

    .status-complete {
      background-color: #dcfce7;
      color: #166534;
    }

    .status-progress {
      background-color: #dbeafe;
      color: #1e40af;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .course-grid {
        grid-template-columns: 1fr;
      }

      .course-details {
        grid-template-columns: 1fr;
      }

      .nav-menu {
        gap: 15px;
      }

      .user-name {
        display: none;
      }

      .page-title {
        font-size: 22px;
      }
    }

    .status-label {
      display: inline-block;
      padding: 6px 12px;
      font-weight: bold;
      border-radius: 5px;
      color: white;
    }

    .status-label.in-progress {
      background-color: #f39c12;
    }

    .status-label.completed {
      background-color: #27ae60;
    }
  </style>
</head>

<body>
  <!-- Header/Navigation -->
  <header class="header">
    <div class="container">
      <nav class="navbar">
        <div class="logo">
          <i class="fas fa-graduation-cap"></i>
          <span><?php echo htmlspecialchars($student_name); ?></span>
        </div>
        <ul class="nav-menu">
          <li class="nav-item">
            <a href="#available-courses" class="nav-link active" data-tab="available-courses">Available Courses</a>
          </li>
          <li class="nav-item">
            <a href="#my-courses" class="nav-link" data-tab="my-courses">My Courses</a>
          </li>
        </ul>
        <div class="user-menu">
          <div class="user-info">
            <div class="user-avatar"><?php echo strtoupper(substr($student_name, 0, 2)); ?></div>
            <span class="user-name"><?php echo htmlspecialchars($student_name); ?></span>
          </div>
        </div>
        <div class="logout-container" style="position: absolute; top: 20px; right: 20px;">
          <form action="logout.php" method="post">
            <button type="submit" class="logout-btn">Logout</button>
          </form>
        </div>
      </nav>
    </div>
  </header>
  <!-- Available Courses Tab -->
  <div class="main-content active" id="available-courses">
    <div class="container">
      <div class="page-header">
        <h1 class="page-title">Available Courses</h1>
        <p class="page-subtitle">Explore courses available for registration</p>
      </div>
      <div class="course-grid">
        <?php
        if (!empty($courses)) {
          foreach ($courses as $course) {
            $course_id    = htmlspecialchars($course['ID']);
            $course_name  = htmlspecialchars($course['Name']);
            $price        = $course['Price'];
            $category     = htmlspecialchars($course['Category']);
            $instructor_id = $course['InstructorID'];
            $description  = htmlspecialchars($course['Description']);
            $description  = (strlen($description) > 150) ? substr($description, 0, 150) . '...' : $description;
            $eta          = htmlspecialchars($course['EstimatedTimeOfCompletion']);

            // Fetch instructor name for the course
            $instructorQuery = "SELECT Name FROM Instructors WHERE ID = ?";
            $stmtInst = $pdo->prepare($instructorQuery);
            $stmtInst->execute([$instructor_id]);
            $instData = $stmtInst->fetch(PDO::FETCH_ASSOC);
            $instructor_name = $instData ? htmlspecialchars($instData['Name']) : 'Unknown Instructor';

            // Check if the student is registered for the course
            $status = null;
            $checkStmt = $pdo->prepare("SELECT StatusOfCompletion FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
            $checkStmt->execute([$course_id, $student_id]);
            $checkData = $checkStmt->fetch(PDO::FETCH_ASSOC);
            if ($checkData) {
              $status = $checkData['StatusOfCompletion'];
            }
        ?>
            <div class="course-card" data-course-id="<?php echo $course_id; ?>">
              <div class="course-banner">
                <i class="fas fa-laptop-code"></i>
                <span class="course-category"><?php echo $category; ?></span>
              </div>
              <div class="course-content">
                <h3 class="course-title"><?php echo $course_name; ?></h3>
                <div class="course-meta">
                  <div class="course-instructor">
                    <i class="fas fa-user"></i>
                    <span><?php echo $instructor_name; ?></span>
                  </div>
                  <div class="course-credits">
                    <i class="fas fa-award"></i>
                    <span><?php echo $price; ?></span>
                  </div>
                </div>
                <p class="course-description"><?php echo $description; ?></p>
                <div class="course-footer">
                  <?php
                  if ($status === null) {
                    echo "<a href='fees.php?course_id={$course_id}&student_id={$student_id}' class='view-details-btn'><i class='fas fa-info-circle'></i> <span>Register</span></a>";
                  } else {
                    $statusClass = (strtolower($status) == 'completed') ? 'status-label completed' : 'status-label in-progress';
                    echo "<span class='{$statusClass}'><i class='fas fa-check-circle'></i> {$status}</span>";
                  }
                  ?>
                  <div class="Time">
                    <i class="far fa-calendar-alt"></i> Time <?php echo $eta; ?>
                  </div>
                </div>
              </div>
            </div>
        <?php
          }
        } else {
          echo "<p>No available courses found.</p>";
        }
        ?>
      </div>
    </div>
  </div>
  <!-- My Courses Tab -->
  <div class="main-content" id="my-courses">
    <div class="container">
      <div class="page-header">
        <h1 class="page-title">My Courses</h1>
        <p class="page-subtitle">View your registered courses and their status</p>
      </div>
      <div class="course-table-container">
        <table class="course-table">
          <thead>
            <tr>
              <th>Course Code</th>
              <th>Course Name</th>
              <th>Instructor</th>
              <th>Start Date</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            if (!empty($registeredCourses)) {
              foreach ($registeredCourses as $reg) {
                $status_class = (mb_strtolower($reg['StatusOfCompletion']) == 'completed') ? 'status-complete' : ((mb_strtolower($reg['StatusOfCompletion']) == 'in progress') ? 'status-progress' : 'status-pending');
                echo "
                        <tr onclick=\"window.location.href='stud_view_course.php?course_id={$reg['CourseID']}&student_id={$student_id}'\" style=\"cursor:pointer;\">
                          <td class='course-code'>{$reg['CourseID']}</td>
                          <td class='course-name'>{$reg['CourseName']}</td>
                          <td>{$reg['InstructorName']}</td>
                          <td>" . date("M d, Y", strtotime($reg['DateOfRegistration'])) . "</td>
                          <td><span class='status-badge {$status_class}'><i class='fas fa-clock'></i> {$reg['StatusOfCompletion']}</span></td>
                        </tr>
                      ";
              }
            } else {
              echo "<tr><td colspan='5'>No registered courses found.</td></tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <!-- JavaScript for Tab Navigation -->
  <script>
    const navLinks = document.querySelectorAll('.nav-link');
    const mainContents = document.querySelectorAll('.main-content');

    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();
        navLinks.forEach(l => l.classList.remove('active'));
        mainContents.forEach(content => content.classList.remove('active'));

        link.classList.add('active');
        const tabId = link.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
      });
    });
  </script>
</body>

</html>