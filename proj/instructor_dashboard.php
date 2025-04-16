<?php
// ATP - instructor_dashboard.php
// Instructor dashboard that displays courses created by the instructor and registration stats.
// Uses PDO via dbh.inc.php.

session_start();
if (!isset($_SESSION['instructor_id'])) {
  header("Location: prof_login.php");
  exit();
}
require_once 'dbh.inc.php';

$instructorID = $_SESSION['instructor_id'];

// Fetch instructor details
$sql = "SELECT * FROM Instructors WHERE ID = :instructor_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['instructor_id' => $instructorID]);
$instructor = $stmt->fetch();
$instructor_name = $instructor ? $instructor['Name'] : '';

$success = "";
$error = "";

// Handle new course creation submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
  $id = trim($_POST['id']);
  $name = trim($_POST['name']);
  $description = trim($_POST['description']);
  $prerequisites = trim($_POST['pre']);
  $category = trim($_POST['category']);
  $difficulty = trim($_POST['difficulty']);
  $eta = trim($_POST['eta']);
  $price = trim($_POST['price']);

  $sql = "INSERT INTO Courses (ID, Name, Category, Difficulty, Description, Price, PreRequsites, EstimatedTimeOfCompletion, InstructorID) 
            VALUES (:id, :name, :category, :difficulty, :description, :price, :prerequisites, :eta, :instructor_id)";
  $stmt = $pdo->prepare($sql);
  try {
    $stmt->execute([
      'id' => $id,
      'name' => $name,
      'category' => $category,
      'difficulty' => $difficulty,
      'description' => $description,
      'price' => $price,
      'prerequisites' => $prerequisites,
      'eta' => $eta,
      'instructor_id' => $instructorID
    ]);
    $success = "Course created successfully!";
  } catch (PDOException $e) {
    if ($e->getCode() == 23000) {
      $error = "Course ID already exists.";
    } else {
      $error = "Error: " . $e->getMessage();
    }
  }
}

// Fetch all courses (for display)
$sql = "SELECT * FROM Courses";
$stmt = $pdo->query($sql);
$courses = $stmt->fetchAll();

// Fetch registration stats for courses by this instructor
$sql = "SELECT 
          cr.CourseID,
          c.Name AS CourseName,
          cr.DateOfRegistration,
          cr.StatusOfCompletion,
          i.Name AS InstructorName
        FROM CourseRegistration cr
        JOIN Courses c ON cr.CourseID = c.ID
        JOIN Instructors i ON c.InstructorID = i.ID
        WHERE cr.StudentID IS NOT NULL AND c.InstructorID = :instructor_id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['instructor_id' => $instructorID]);
$registrations = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- ATP - instructor_dashboard.php -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instructor Dashboard</title>
  <!-- Bootstrap 5 CSS from CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Font Awesome CDN -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    /* New color scheme for dashboard */
    body {
      background-color: #f7f9fa;
      font-family: 'Segoe UI', sans-serif;
    }

    .navbar {
      background-color: #2c7873;
      color: white;
      padding: 1rem 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar-brand {
      font-size: 1.8rem;
      font-weight: bold;
    }

    .nav-menu {
      list-style: none;
      display: flex;
      gap: 1.5rem;
    }

    .nav-menu li a {
      color: white;
      text-decoration: none;
      font-size: 1rem;
    }

    .logout-btn {
      background-color: #e74c3c;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 6px;
      cursor: pointer;
    }

    .header {
      margin: 2rem 0;
      text-align: center;
    }

    .header h1 {
      color: #2c7873;
    }

    .stats-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: #fff;
      padding: 1rem;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .course-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 1.5rem;
    }

    .course-card {
      background: #fff;
      padding: 1rem;
      border-radius: 6px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
      transition: transform 0.3s;
      cursor: pointer;
    }

    .course-card:hover {
      transform: translateY(-5px);
    }
  </style>
</head>

<body>
  <!-- Header Navigation -->
  <nav class="navbar">
    <div class="navbar-brand">EduTeach - <?php echo htmlspecialchars($instructor_name); ?></div>
    <ul class="nav-menu">
      <li><a href="#courses" class="nav-link active" data-tab="courses">My Courses</a></li>
      <li><a href="#add-course" class="nav-link" data-tab="add-course">Add New Course</a></li>
    </ul>
    <button class="logout-btn" onclick="window.location.href='logout.php'">Logout</button>
  </nav>

  <div class="container">
    <!-- My Courses Tab -->
    <div id="courses" class="main-content active">
      <div class="header">
        <h1>My Courses</h1>
      </div>
      <div class="stats-cards">
        <div class="stat-card">
          <h2><?php echo count($courses); ?></h2>
          <p>Total Courses</p>
        </div>
        <div class="stat-card">
          <h2><?php echo count($registrations); ?></h2>
          <p>Total Registrations</p>
        </div>
      </div>
      <div class="course-grid">
        <?php foreach ($courses as $course): ?>
          <div class="course-card" onclick="window.location.href='stud_view_course.php?course_id=<?php echo $course['ID']; ?>&student_id=<?php echo $_SESSION['student_id'] ?? ''; ?>'">
            <h3><?php echo htmlspecialchars($course['Name']); ?></h3>
            <p><?php echo (strlen($course['Description']) > 100) ? substr($course['Description'], 0, 100) . '...' : $course['Description']; ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Add New Course Tab -->
    <div id="add-course" class="main-content" style="display:none;">
      <div class="header">
        <h1>Add New Course</h1>
      </div>
      <div class="add-course-form">
        <?php if ($success) echo "<p style='color: green;'>$success</p>"; ?>
        <?php if ($error) echo "<p style='color: red;'>$error</p>"; ?>
        <form method="POST" action="">
          <div class="mb-3">
            <label for="id" class="form-label">Course ID</label>
            <input type="text" id="id" name="id" class="form-control" placeholder="Enter course ID" required>
          </div>
          <div class="mb-3">
            <label for="name" class="form-label">Course Title</label>
            <input type="text" id="name" name="name" class="form-control" placeholder="Enter course title" required>
          </div>
          <div class="mb-3">
            <label for="description" class="form-label">Course Description</label>
            <textarea id="description" name="description" class="form-control" placeholder="Enter course description" required></textarea>
          </div>
          <div class="mb-3">
            <label for="pre" class="form-label">Prerequisites</label>
            <textarea id="pre" name="pre" class="form-control" placeholder="Enter course prerequisites" required></textarea>
          </div>
          <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select id="category" name="category" class="form-control" required>
              <option value="">Select a category</option>
              <option value="programming">Programming</option>
              <option value="data-science">Data Science</option>
              <option value="web-development">Web Development</option>
              <option value="ai">Artificial Intelligence</option>
              <option value="design">Design</option>
              <option value="business">Business</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="difficulty" class="form-label">Level</label>
            <select id="difficulty" name="difficulty" class="form-control" required>
              <option value="">Select a level</option>
              <option value="beginner">Beginner</option>
              <option value="intermediate">Intermediate</option>
              <option value="advanced">Advanced</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="eta" class="form-label">Estimated Time of Completion</label>
            <input type="text" id="eta" name="eta" class="form-control" placeholder="Enter course ETA" required>
          </div>
          <div class="mb-3">
            <label for="price" class="form-label">Course Price</label>
            <input type="text" id="price" name="price" class="form-control" placeholder="Enter course price" required>
          </div>
          <button type="submit" class="btn btn-primary">Create Course</button>
        </form>
      </div>
    </div>
  </div>

  <script>
    // Simple tab switching functionality
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('click', function(e) {
        e.preventDefault();
        const tab = this.getAttribute('data-tab');
        document.querySelectorAll('.main-content').forEach(mc => mc.style.display = 'none');
        document.getElementById(tab).style.display = 'block';
        document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
        this.classList.add('active');
      });
    });
  </script>
</body>

</html>