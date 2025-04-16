<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$password = "";
$db = "DA215_Project2";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

// Get instructor ID from session
if (isset($_SESSION['instructor_id'])) {
    $instructorID = $_SESSION['instructor_id'];
    // Get the total number of students registered for the instructor's courses
    $stmt = $conn->prepare("
        SELECT COUNT(DISTINCT CourseRegistration.StudentID) AS TotalStudents
        FROM CourseRegistration
        INNER JOIN Courses ON CourseRegistration.CourseID = Courses.ID
        WHERE Courses.InstructorID = ?
    ");
    $stmt->bind_param("s", $instructorID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $totalStudents = $row['TotalStudents'];
    }
    $stmt->close();

    // Get the total number of courses by this instructor
    $stmt = $conn->prepare("SELECT COUNT(*) AS CourseCount FROM Courses WHERE InstructorID = ?");
    $stmt->bind_param("s", $instructorID);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $totalCourses = $row['CourseCount'];
    }
    $stmt->close();
   
} else {
    $error = "Instructor not logged in.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['instructor_id'])) {
        $error = "Instructor not logged in.";
    } else {

        
        $instructorID = $_SESSION['instructor_id'];
        if(isset($_POST['id'])){
            // Get form data
            $id = trim($_POST['id']);
            $name = trim($_POST['name']);
            $description = trim($_POST['description']);
            $prerequisites = trim($_POST['pre']);
            $category = $_POST['category'];
            $difficulty = $_POST['difficulty'];
            $eta = trim($_POST['eta']);
            $price = trim($_POST['price']);

            // Prepare and execute insert
            $stmt = $conn->prepare("INSERT INTO Courses (ID, `Name`, Category, Difficulty, `Description`, Price, PreRequsites, EstimatedTimeOfCompletion, InstructorID) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sssssssss", $id, $name, $category, $difficulty, $description, $price, $prerequisites, $eta, $instructorID);

            if ($stmt->execute()) {
                $success = "Course created successfully!";
            } else {
                if ($conn->errno == 1062) {
                    $error = "Course ID already exists.";
                } else {
                    $error = "Error: " . $stmt->error;
                }
            }

            $stmt->close();

        }

    }
}







?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instructor Dashboard</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
      background-color: #f5f7fa;
    }
    
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
    
    .navbar-items a:hover, .navbar-items a.active {
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
    
    .search-bar {
      display: flex;
      gap: 0.5rem;
    }
    
    .search-bar input {
      padding: 0.5rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      width: 250px;
    }
    
    .search-bar button {
      background-color: #3498db;
      color: white;
      border: none;
      padding: 0.5rem 1rem;
      border-radius: 4px;
      cursor: pointer;
    }
    
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
      font-size: 2rem;
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
    
    .add-course-form {
      background-color: white;
      padding: 2rem;
      border-radius: 8px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .form-group {
      margin-bottom: 1.5rem;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      color: #2c3e50;
      font-weight: 500;
    }
    
    .form-group input, .form-group textarea, .form-group select {
      width: 100%;
      padding: 0.75rem;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 1rem;
    }
    
    .form-group textarea {
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
    
    .tab-content {
      display: none;
    }
    
    .tab-content.active {
      display: block;
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
    .logout-btn {
    padding: 10px 18px;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 15px;
    cursor: pointer;
    transition: background-color 0.3s ease;
    margin: 20px;
}

.logout-btn:hover {
    background-color: #c82333;
}
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="navbar-brand">EduTeach</div>
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
          <div class="stat-number"><?php echo $totalCourses; ?></div>
          <div class="stat-label">Total Courses</div>
        </div>
        <div class="stat-card">
          <div class="stat-number"><?php echo $totalStudents; ?></div>
          <div class="stat-label">Total Students</div>
        </div>
      </div>
      
      <div class="courses-grid">
        <!-- Course Card 1 -->

        <!--<div class="course-card">
          <div class="course-image">
            <span>JS</span>
          </div>
          <div class="course-content">
            <h3 class="course-title">Introduction to JavaScript</h3>
            <p class="course-info">Learn the basics of JavaScript programming language</p>
            <div class="course-stats">
              <span>42 Students</span>
            </div>
            <div class="course-actions">
              <button class="edit-btn">Edit</button>
              <button class="view-btn">View</button>
            </div>
          </div>
        </div>-->


        <?php

$host = "localhost";
$user = "root";
$password = "";
$db = "DA215_Project2";

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['instructor_id'])) {
    echo "<p>Instructor not logged in.</p>";
    exit();
}

$instructorID = $_SESSION['instructor_id'];

$stmt = $conn->prepare("
    SELECT c.ID, c.Name, c.Description,
           COUNT(cr.StudentID) AS StudentCount
    FROM Courses c
    LEFT JOIN CourseRegistration cr ON c.ID = cr.CourseID
    WHERE c.InstructorID = ?
    GROUP BY c.ID
");
$stmt->bind_param("s", $instructorID);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $courseName = htmlspecialchars($row['Name']);
    $courseID=htmlspecialchars($row['ID']);
    $description = htmlspecialchars($row['Description']);
    $description = (strlen($description) > 150) ? substr($description, 0, 150) . '...' : $description;
    $students = $row['StudentCount'];
    $initials = strtoupper(substr($courseName, 0, 2)); // initials for image box

    echo "
<div class='course-card'>
    <div class='course-image'>
        <span>$courseID</span>
    </div>
    <div class='course-content'>
        <h3 class='course-title'>$courseName</h3>
        <p class='course-info'>$description</p>
        <div class='course-stats'>
            <span>$students Students</span>
        </div>
        <div class='course-actions'>
            <button class='view-btn' onclick=\"window.location.href='view_course.php?id=$courseID'\">View</button>
        </div>
    </div>
</div>
";
}

$stmt->close();
$conn->close();
?>


      </div>
    </div>
    
    <!-- Add New Course Tab -->

  <div id="add-course-tab" class="tab-content">
    <div class="header">
      <h1>Add New Course</h1>
    </div>
    
    <div class="add-course-form">
    <form method="POST" action="">

      <?php if (!empty($success)) echo "<p style='color: green;'>$success</p>"; ?>
      <?php if (!empty($error)) echo "<p style='color: red;'>$error</p>"; ?>

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
      
      // Update active tab button
      coursesTabBtn.classList.add('active');
      addCourseTabBtn.classList.remove('active');
      
      // Show corresponding tab content
      coursesTab.classList.add('active');
      addCourseTab.classList.remove('active');
    });
    
    addCourseTabBtn.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Update active tab button
      addCourseTabBtn.classList.add('active');
      coursesTabBtn.classList.remove('active');
      
      // Show corresponding tab content
      addCourseTab.classList.add('active');
      coursesTab.classList.remove('active');
    });
  </script>

<script>
document.getElementById('courses-tab-btn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('courses-tab').classList.add('active');
    document.getElementById('add-course-tab').classList.remove('active');

    this.classList.add('active');
    document.getElementById('add-course-tab-btn').classList.remove('active');
});

document.getElementById('add-course-tab-btn').addEventListener('click', function(e) {
    e.preventDefault();
    document.getElementById('courses-tab').classList.remove('active');
    document.getElementById('add-course-tab').classList.add('active');

    this.classList.add('active');
    document.getElementById('courses-tab-btn').classList.remove('active');
});
</script>

<!-- Edit Content Modal -->
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