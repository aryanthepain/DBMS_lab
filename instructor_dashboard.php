<?php

/**
 * File: instructor_dashboard.php
 * Author: Aryan Gupta
 *
 * Instructor dashboard for eLearn using a PDO connection.
 */

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once 'dbh.inc.php'; // This file creates a PDO connection stored in $pdo

$success = "";
$error   = "";

// Ensure instructor is logged in
if (isset($_SESSION['instructor_id'])) {
  $instructorID = $_SESSION['instructor_id'];

  // Get total number of students registered for the instructor's courses
  $stmt = $pdo->prepare("
        SELECT COUNT(DISTINCT CourseRegistration.StudentID) AS TotalStudents
        FROM CourseRegistration
        INNER JOIN Courses ON CourseRegistration.CourseID = Courses.ID
        WHERE Courses.InstructorID = ?
    ");
  $stmt->execute([$instructorID]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $totalStudents = $row ? $row['TotalStudents'] : 0;

  // Get total number of courses by this instructor
  $stmt = $pdo->prepare("SELECT COUNT(*) AS CourseCount FROM Courses WHERE InstructorID = ?");
  $stmt->execute([$instructorID]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  $totalCourses = $row ? $row['CourseCount'] : 0;
} else {
  $error = "Instructor not logged in.";
}

// Handle new course creation via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (!isset($_SESSION['instructor_id'])) {
    $error = "Instructor not logged in.";
  } else {
    $instructorID = $_SESSION['instructor_id'];
    if (isset($_POST['id'])) {
      // Get form data
      $courseID      = trim($_POST['id']);
      $courseName    = trim($_POST['name']);
      $description   = trim($_POST['description']);
      $prerequisites = trim($_POST['pre']);
      $category      = $_POST['category'];
      $difficulty    = $_POST['difficulty'];
      $eta           = trim($_POST['eta']);
      $price         = trim($_POST['price']);

      // Prepare and execute insert query using PDO
      $sql = "INSERT INTO Courses (ID, Name, Category, Difficulty, Description, Price, PreRequsites, EstimatedTimeOfCompletion, InstructorID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
      $stmt = $pdo->prepare($sql);
      try {
        $stmt->execute([$courseID, $courseName, $category, $difficulty, $description, $price, $prerequisites, $eta, $instructorID]);
        $success = "Course created successfully!";
      } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
          $error = "Course ID already exists.";
        } else {
          $error = "Error: " . $e->getMessage();
        }
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Instructor Dashboard - eLearn</title>
  <style>
    /* Global styles */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: Arial, sans-serif;
    }

    body {
      background: linear-gradient(135deg, #eef2f3 0%, #8e9eab 100%);
    }

    /* Navbar */
    .navbar {
      background-color: #2c3e50;
      color: white;
      padding: 1rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
    }

    .navbar-items {
      display: flex;
      gap: 1rem;
    }

    .navbar-items a {
      color: white;
      text-decoration: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      transition: background-color 0.3s;
    }

    .navbar-items a:hover,
    .navbar-items a.active {
      background-color: #34495e;
    }

    .logout-btn {
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .logout-btn:hover {
      background-color: #c0392b;
    }

    /* Main container */
    .container {
      max-width: 1200px;
      margin: 0 auto;
      padding: 2rem;
    }

    .header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 2rem;
    }

    .header h1 {
      color: #2c3e50;
    }

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background-color: white;
      padding: 1.5rem;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .stat-number {
      font-size: 2rem;
      font-weight: bold;
      color: #2c3e50;
      margin-bottom: 0.5rem;
    }

    .stat-label {
      color: #7f8c8d;
    }

    /* Tabs */
    .tab-content {
      display: none;
    }

    .tab-content.active {
      display: block;
    }

    /* Courses grid */
    .courses-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 2rem;
    }

    .course-card {
      background-color: white;
      border-radius: 8px;
      overflow: hidden;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
    }

    .course-card:hover {
      transform: translateY(-5px);
    }

    .course-image {
      height: 160px;
      background-color: #3498db;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 1.5rem;
    }

    .course-content {
      padding: 1.5rem;
    }

    .course-title {
      font-size: 1.25rem;
      margin-bottom: 0.5rem;
      color: #2c3e50;
    }

    .course-info {
      color: #7f8c8d;
      margin-bottom: 1rem;
    }

    .course-stats {
      display: flex;
      justify-content: space-between;
      color: #7f8c8d;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }

    .course-actions {
      display: flex;
      gap: 0.5rem;
    }

    .course-actions button {
      flex: 1;
      padding: 0.5rem;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .edit-btn {
      background-color: #f39c12;
      color: white;
    }

    .edit-btn:hover {
      background-color: #d35400;
    }

    .view-btn {
      background-color: #2ecc71;
      color: white;
    }

    .view-btn:hover {
      background-color: #27ae60;
    }

    /* Add Course Form */
    .add-course-form {
      background-color: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .add-course-form .form-group {
      margin-bottom: 1.5rem;
    }

    .add-course-form label {
      display: block;
      margin-bottom: 0.5rem;
      color: #2c3e50;
      font-weight: 500;
    }

    .add-course-form input,
    .add-course-form textarea,
    .add-course-form select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }

    .add-course-form textarea {
      min-height: 150px;
      resize: vertical;
    }

    .submit-btn {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      border-radius: 4px;
      font-size: 1rem;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .submit-btn:hover {
      background-color: #2980b9;
    }

    /* Logout button spacing already defined in navbar */
  </style>
</head>

<body>
  <nav class="navbar">
    <div class="navbar-brand">eLearn</div>
    <div class="navbar-items">
      <a href="#" class="active" id="courses-tab-btn">My Courses</a>
      <a href="#" id="add-course-tab-btn">Add New Course</a>
    </div>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </nav>

  <div class="container">
    <!-- My Courses Tab -->
    <div id="courses-tab" class="tab-content active">
      <div class="header">
        <h1>My Courses</h1>
      </div>

      <div class="stats-cards">
        <div class="stat-card">
          <div class="stat-number"><?php echo isset($totalCourses) ? $totalCourses : 0; ?></div>
          <div class="stat-label">Total Courses</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?php echo isset($totalStudents) ? $totalStudents : 0; ?></div>
          <div class="stat-label">Total Students</div>
        </div>
      </div>

      <div class="courses-grid">
        <?php
        if (!isset($_SESSION['instructor_id'])) {
          echo "<p>Instructor not logged in.</p>";
          exit();
        }
        $stmt = $pdo->prepare("
            SELECT c.ID, c.Name, c.Description, COUNT(cr.StudentID) AS StudentCount
            FROM Courses c
            LEFT JOIN CourseRegistration cr ON c.ID = cr.CourseID
            WHERE c.InstructorID = ?
            GROUP BY c.ID
        ");
        $stmt->execute([$instructorID]);
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          $courseID   = htmlspecialchars($row['ID']);
          $courseName = htmlspecialchars($row['Name']);
          $description = htmlspecialchars($row['Description']);
          $description = (strlen($description) > 150) ? substr($description, 0, 150) . '...' : $description;
          $studentCount = $row['StudentCount'];

          echo "
            <div class='course-card'>
                <div class='course-image'>
                    <span>{$courseID}</span>
                </div>
                <div class='course-content'>
                    <h3 class='course-title'>{$courseName}</h3>
                    <p class='course-info'>{$description}</p>
                    <div class='course-stats'>
                        <span>{$studentCount} Students</span>
                    </div>
                    <div class='course-actions'>
                        <button class='view-btn' onclick=\"window.location.href='view_course.php?id={$courseID}'\">View</button>
                    </div>
                </div>
            </div>
            ";
        }
        ?>
      </div>
    </div>

    <!-- Add New Course Tab -->
    <div id="add-course-tab" class="tab-content">
      <div class="header">
        <h1>Add New Course</h1>
      </div>
      <div class="add-course-form">
        <?php
        if (!empty($success)) echo "<p style='color: green;'>{$success}</p>";
        if (!empty($error)) echo "<p style='color: red;'>{$error}</p>";
        ?>
        <form method="POST" action="">
          <div class="form-group">
            <label for="id">Course ID</label>
            <input type="text" id="id" name="id" placeholder="Enter course ID" required>
          </div>
          <div class="form-group">
            <label for="name">Course Title</label>
            <input type="text" id="name" name="name" placeholder="Enter course title" required>
          </div>
          <div class="form-group">
            <label for="description">Course Description</label>
            <textarea id="description" name="description" placeholder="Enter course description" required></textarea>
          </div>
          <div class="form-group">
            <label for="pre">Course Prerequisites</label>
            <textarea id="pre" name="pre" placeholder="Enter course prerequisites" required></textarea>
          </div>
          <div class="form-group">
            <label for="category">Category</label>
            <select id="category" name="category" required>
              <option value="">Select a category</option>
              <option value="programming">Programming</option>
              <option value="data-science">Data Science</option>
              <option value="web-development">Web Development</option>
              <option value="ai">Artificial Intelligence</option>
              <option value="design">Design</option>
              <option value="business">Business</option>
            </select>
          </div>
          <div class="form-group">
            <label for="difficulty">Level</label>
            <select id="difficulty" name="difficulty" required>
              <option value="">Select a level</option>
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
            </select>
          </div>
          <div class="form-group">
            <label for="eta">Estimated Time of Completion</label>
            <input type="text" id="eta" name="eta" placeholder="Enter course ETA" required>
          </div>
          <div class="form-group">
            <label for="price">Course Price</label>
            <input type="text" id="price" name="price" placeholder="Enter course price" required>
          </div>
          <button class="submit-btn" type="submit">Create Course</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Tab switching functionality
    const coursesTabBtn = document.getElementById('courses-tab-btn');
    const addCourseTabBtn = document.getElementById('add-course-tab-btn');
    const coursesTab = document.getElementById('courses-tab');
    const addCourseTab = document.getElementById('add-course-tab');

    coursesTabBtn.addEventListener('click', function(e) {
      e.preventDefault();
      coursesTabBtn.classList.add('active');
      addCourseTabBtn.classList.remove('active');
      coursesTab.classList.add('active');
      addCourseTab.classList.remove('active');
    });

    addCourseTabBtn.addEventListener('click', function(e) {
      e.preventDefault();
      addCourseTabBtn.classList.add('active');
      coursesTabBtn.classList.remove('active');
      addCourseTab.classList.add('active');
      coursesTab.classList.remove('active');
    });
  </script>

  <!-- Edit Content Modal (if needed for further interactions) -->
  <div id="edit-form" style="display:none; position:fixed; top:10%; left:50%; transform:translateX(-50%);
       background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.3); z-index:1000; max-width:500px;">
    <form method="POST" action="">
      <input type="hidden" name="course_id" id="course_id_input">
      <label for="name">Content Title:</label>
      <input type="text" name="name" id="name" required style="width:100%; margin-bottom:10px;" required>
      <label for="content">Content (Text or Link):</label>
      <textarea name="content" id="content" rows="5" required style="width:100%; margin-bottom:10px;" required></textarea>
      <button type="submit">Add Content</button>
      <button type="button" onclick="closeForm()">Cancel</button>
    </form>
  </div>

  <script>
    function editCourse(courseID) {
      document.getElementById("edit-form").style.display = "block";
      document.getElementById("course_id_input").value = courseID;
    }

    function closeForm() {
      document.getElementById("edit-form").style.display = "none";
    }
  </script>
</body>

</html>