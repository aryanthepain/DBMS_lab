<?php
// ATP - student_dashboard.php
// Student Dashboard: Displays registered courses and a list of available courses (not yet registered).
// Uses PDO via dbh.inc.php and session handling to fetch data for the logged-in student.

session_start();
require_once 'dbh.inc.php';

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
  header("Location: index.php");
  exit();
}
$student_id = $_SESSION['student_id'];

// Fetch student details
$sql = "SELECT Name FROM Students WHERE ID = :student_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['student_id' => $student_id]);
$student = $stmt->fetch();
$student_name = $student ? $student['Name'] : '';

// Query for registered courses for this student
$sql_registered = "SELECT 
    cr.CourseID,
    c.Name AS CourseName,
    c.Description,
    cr.DateOfRegistration,
    cr.StatusOfCompletion,
    i.Name AS InstructorName
FROM CourseRegistration cr
JOIN Courses c ON cr.CourseID = c.ID
JOIN Instructors i ON c.InstructorID = i.ID
WHERE cr.StudentID = :student_id
ORDER BY cr.DateOfRegistration DESC";
$stmt = $pdo->prepare($sql_registered);
$stmt->execute(['student_id' => $student_id]);
$registeredCourses = $stmt->fetchAll();

// Query for available courses (not registered by this student)
$sql_available = "SELECT c.ID, c.Name, c.Description, i.Name AS InstructorName
                  FROM Courses c
                  JOIN Instructors i ON c.InstructorID = i.ID
                  WHERE c.ID NOT IN (
                      SELECT CourseID FROM CourseRegistration WHERE StudentID = :student_id
                  )
                  ORDER BY c.Name ASC";
$stmt = $pdo->prepare($sql_available);
$stmt->execute(['student_id' => $student_id]);
$availableCourses = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- ATP - student_dashboard.php -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
  <!-- Bootstrap 5 CSS from CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* Remixed Dashboard Styles */
    body {
      background-color: #f0f4f8;
      font-family: 'Segoe UI', sans-serif;
    }

    .navbar-custom {
      background-color: #2c7873;
      padding: 1rem;
    }

    .navbar-custom .navbar-brand,
    .navbar-custom .nav-link {
      color: white !important;
    }

    .section-header {
      background-color: #2c7873;
      color: white;
      padding: 15px;
      border-radius: 4px;
      margin-bottom: 20px;
    }

    .course-card {
      border: 1px solid #ddd;
      padding: 15px;
      margin-bottom: 15px;
      border-radius: 4px;
      background-color: #fff;
    }

    .course-card:hover {
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-register {
      background-color: #2c7873;
      color: #fff;
      border: none;
      padding: 8px 12px;
      border-radius: 4px;
      text-decoration: none;
    }

    .btn-register:hover {
      background-color: #23635b;
    }
  </style>
</head>

<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container">
      <a class="navbar-brand" href="#">ATP - EduLearn</a>
      <div class="d-flex">
        <span class="me-3 text-white">Welcome, <?php echo htmlspecialchars($student_name); ?></span>
        <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
      </div>
    </div>
  </nav>

  <div class="container my-4">
    <!-- Registered Courses Section -->
    <div class="section-header">
      <h3>Registered Courses</h3>
    </div>
    <?php if (count($registeredCourses) > 0): ?>
      <?php foreach ($registeredCourses as $reg): ?>
        <div class="course-card" onclick="window.location.href='view_course.php?course_id=<?php echo urlencode($reg['CourseID']); ?>&student_id=<?php echo urlencode($student_id); ?>'" style="cursor:pointer;">
          <h4><?php echo htmlspecialchars($reg['CourseName']); ?></h4>
          <p><?php echo (strlen($reg['Description']) > 100) ? substr($reg['Description'], 0, 100) . '...' : $reg['Description']; ?></p>
          <p><strong>Instructor:</strong> <?php echo htmlspecialchars($reg['InstructorName']); ?></p>
          <p><strong>Status:</strong> <?php echo htmlspecialchars($reg['StatusOfCompletion'] ?: 'In Progress'); ?></p>
          <p><small><?php echo date("M d, Y", strtotime($reg['DateOfRegistration'])); ?></small></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No registered courses found.</p>
    <?php endif; ?>

    <!-- Available Courses Section -->
    <div class="section-header mt-5">
      <h3>Available Courses</h3>
    </div>
    <?php if (count($availableCourses) > 0): ?>
      <?php foreach ($availableCourses as $course): ?>
        <div class="course-card" onclick="window.location.href='fees.php?course_id=<?php echo urlencode($course['ID']); ?>&student_id=<?php echo urlencode($student_id); ?>'" style="cursor:pointer;">
          <h4><?php echo htmlspecialchars($course['Name']); ?></h4>
          <p><?php echo (strlen($course['Description']) > 100) ? substr($course['Description'], 0, 100) . '...' : $course['Description']; ?></p>
          <p><strong>Instructor:</strong> <?php echo htmlspecialchars($course['InstructorName'] ?? ''); ?></p>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>No available courses found.</p>
    <?php endif; ?>
  </div>
  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>