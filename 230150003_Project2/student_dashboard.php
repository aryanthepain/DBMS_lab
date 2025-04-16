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

$student_id = $_SESSION['student_id'];


$sql00 = "SELECT * FROM Students WHERE ID='$student_id'";
$res00 = $conn->query($sql00);
$row00 = $res00->fetch_assoc();
$student_name = $row00['Name'];

$sql = "SELECT * FROM Courses"; // Modify if you need to add any filters (e.g., only active courses)
$result = $conn->query($sql);



// SQL to get registered courses
$sql1 = "
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

$stmt1 = $conn->prepare($sql1);
$stmt1->bind_param("s", $student_id);
$stmt1->execute();
$result1 = $stmt1->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Dashboard</title>
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
      /*display: -webkit-box;
      -webkit-line-clamp: 3;*/
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

    /* My Courses Table */
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

    .status-completed {
      background-color: #dcfce7;
      color: #166534;
    }

    .status-progress {
      background-color: #dbeafe;
      color: #1e40af;
    }

    /* Course Details Modal */
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      backdrop-filter: blur(5px);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s;
    }

    .modal.active {
      opacity: 1;
      visibility: visible;
    }

    .modal-content {
      background: var(--bg-white);
      width: 700px;
      max-width: 90%;
      border-radius: var(--radius);
      overflow: hidden;
      max-height: 90vh;
      display: flex;
      flex-direction: column;
    }

    .modal-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
      color: white;
      padding: 20px 30px;
      position: relative;
    }

    .modal-title {
      font-size: 22px;
      font-weight: 600;
    }

    .modal-subtitle {
      opacity: 0.8;
      margin-top: 5px;
      font-size: 15px;
    }

    .close-modal {
      position: absolute;
      top: 20px;
      right: 20px;
      background: rgba(255, 255, 255, 0.2);
      border: none;
      color: white;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: all 0.3s;
    }

    .close-modal:hover {
      background: rgba(255, 255, 255, 0.4);
    }

    .modal-body {
      padding: 30px;
      overflow-y: auto;
    }

    .course-details {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 20px;
      margin-bottom: 25px;
    }

    .course-detail-item {
      margin-bottom: 5px;
    }

    .detail-label {
      font-weight: 600;
      color: var(--text-dark);
      font-size: 14px;
      margin-bottom: 5px;
      display: block;
    }

    .detail-value {
      color: var(--text-light);
      font-size: 15px;
    }

    .course-full-description {
      margin-top: 20px;
    }

    .course-full-description h4 {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 10px;
      color: var(--dark);
    }

    .course-full-description p {
      color: var(--text-light);
      line-height: 1.6;
      margin-bottom: 15px;
    }

    .modal-footer {
      padding: 20px 30px;
      border-top: 1px solid #f1f5f9;
      display: flex;
      justify-content: flex-end;
    }

    .register-course-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 20px;
      background-color: var(--primary);
      color: white;
      border: none;
      border-radius: var(--radius);
      font-weight: 500;
      cursor: pointer;
      transition: background-color 0.3s;
      font-size: 15px;
    }

    .register-course-btn:hover {
      background-color: var(--primary-light);
    }

    .empty-state {
      text-align: center;
      padding: 50px 20px;
    }

    .empty-state i {
      font-size: 50px;
      color: #cbd5e1;
      margin-bottom: 20px;
    }

    .empty-state h3 {
      font-size: 20px;
      color: var(--text-dark);
      margin-bottom: 10px;
    }

    .empty-state p {
      color: var(--text-light);
      max-width: 400px;
      margin: 0 auto;
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
      /* orange */
    }

    .status-label.completed {
      background-color: #27ae60;
      /* green */
    }
  </style>
</head>

<body>
  <!-- Header/Navigation -->
  <header class="header">

    <div class='container'>
      <nav class='navbar'>
        <div class='logo'>
          <i class='fas fa-graduation-cap'></i>
          <span><?php echo $student_name; ?></span>
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
            <div class="user-avatar"></div>
            <span class="user-name"></span>
          </div>
        </div>

        <div class="logout-container" style="position: absolute; top: 20px; right: 20px;">

          <form action="logout.php" method="post">
            <button type="submit" class="logout-button" style="
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.3s ease;
          ">Logout</button>
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


        // Check if courses exist
        if ($result->num_rows > 0) {
          // Output each course
          while ($row = $result->fetch_assoc()) {
            // Fetch course data
            $price = $row['Price'];
            $course_id = $row['ID'];
            $course_name = $row['Name'];
            $category = $row['Category'];
            $instructor_id = $row['InstructorID'];
            $description = $row['Description'];
            $description = (strlen($description) > 150) ? substr($description, 0, 150) . '...' : $description;
            $eta = $row['EstimatedTimeOfCompletion']; // Assuming you have a StartDate field

            // Fetch instructor name (you can join tables if needed)
            $instructor_query = "SELECT Name FROM instructors WHERE ID = '$instructor_id'";
            $instructor_result = $conn->query($instructor_query);
            $instructor_name = ($instructor_result->num_rows > 0) ? $instructor_result->fetch_assoc()['Name'] : 'Unknown Instructor';
            // Check if the student is already registered for this course
            $status = null;
            $checkStmt = $conn->prepare("SELECT StatusOfCompletion FROM CourseRegistration WHERE CourseID = ? AND StudentID = ?");
            $checkStmt->bind_param("ss", $course_id, $student_id);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            if ($checkRow = $checkResult->fetch_assoc()) {
              $status = $checkRow['StatusOfCompletion'];
            }
            $checkStmt->close();
            // Display each course as a card
            echo "
            <div class='course-card' data-course-id='$course_id'>
              <div class='course-banner'>
                <i class='fas fa-laptop-code'></i>
                <span class='course-category'>$category</span>
              </div>
              <div class='course-content'>
                <h3 class='course-title'>$course_name</h3>
                <div class='course-meta'>
                  <div class='course-instructor'>
                    <i class='fas fa-user'></i>
                    <span>$instructor_name</span>
                  </div>
                  <div class='course-credits'>
                    <i class='fas fa-award'></i>
                    <span>$price</span>
                  </div>
                </div>
                <p class='course-description'>$description</p>
                <div class='course-footer'>

                    " . (
              $status === null
              ? "<a href='fees.php?course_id=$course_id&student_id=$student_id' class='view-details-btn'>
                                <i class='fas fa-info-circle'></i>
                                <span>Register</span>
                            </a>"
              : "<span class='status-label " . ($status === 'Completed' ? "completed" : "in-progress") . "'>
                                <i class='fas fa-check-circle'></i> $status
                            </span>"
            ) . "
                  <div class='Time'>
                    <i class='far fa-calendar-alt'></i> Time $eta
                  </div>
                </div>
              </div>
            </div>
          ";
          }
        } else {
          echo "<p>No available courses found.</p>";
        }

        // Close the database connection
        $conn->close();
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


            if ($result1->num_rows > 0) {
              while ($row1 = $result1->fetch_assoc()) {
                $status_class = strtolower($row1['StatusOfCompletion']) == 'completed' ? 'status-complete' : (strtolower($row1['StatusOfCompletion']) == 'in progress' ? 'status-progress' : 'status-pending');

                echo "
                <tr onclick=\"window.location.href='stud_view_course.php?course_id={$row1['CourseID']}&student_id=$student_id'\" style=\"cursor:pointer;\">
                <td class='course-code'>{$row1['CourseID']}</td>
                <td class='course-name'>{$row1['CourseName']}</td>
                <td>{$row1['InstructorName']}</td>
                <td>" . date("M d, Y", strtotime($row1['DateOfRegistration'])) . "</td>
                <td><span class='status-badge $status_class'><i class='fas fa-clock'></i> {$row1['StatusOfCompletion']}</span></td>
              </tr>
                ";
              }
            } else {
              echo "<tr><td colspan='5'>No registered courses found.</td></tr>";
            }

            $stmt1->close();
            ?>


          </tbody>
        </table>

      </div>
    </div>
  </div>


  <!-- JavaScript for functionality -->
  <script>
    // Tab Navigation
    const navLinks = document.querySelectorAll('.nav-link');
    const mainContent = document.querySelectorAll('.main-content');

    navLinks.forEach(link => {
      link.addEventListener('click', (e) => {
        e.preventDefault();

        // Remove active class from all links and contents
        navLinks.forEach(l => l.classList.remove('active'));
        mainContent.forEach(content => content.classList.remove('active'));

        // Add active class to clicked link and corresponding content
        link.classList.add('active');
        const tabId = link.getAttribute('data-tab');
        document.getElementById(tabId).classList.add('active');
      });
    });
  </script>

</body>

</html>