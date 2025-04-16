<?php
session_start();
$host = "localhost";
$user = "root";
$password = "";
$db = "DA215_Project2";

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password_input = $_POST['password'];

  $sql = "SELECT * FROM Students WHERE Username = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $username);

  if ($stmt->execute()) {
      $result = $stmt->get_result();
      if ($result->num_rows == 1) {
          $student = $result->fetch_assoc();

          // Directly compare plain text passwords
          if ($password_input === $student['Password']) {
              $_SESSION['student_id'] = $student['ID'];
              $_SESSION['student_name'] = $student['Name'];
              header("Location: student_dashboard.php");
              exit();
          } else {
              $error = "Invalid password.";
          }
      } else {
          $error = "Student not found.";
      }
  } else {
      $error = "Database error.";
  }
  $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Login</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    
    .login-box {
      width: 400px;
      background: white;
      padding: 40px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
    }
    
    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .login-header h2 {
      color: #2d3748;
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    .login-header p {
      color: #718096;
      font-size: 16px;
    }
    
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 500;
      color: #4a5568;
    }
    
    .form-control {
      width: 100%;
      padding: 14px;
      border: 1px solid #e2e8f0;
      border-radius: 8px;
      font-size: 16px;
      transition: all 0.3s ease;
    }
    
    .form-control:focus {
      outline: none;
      border-color: #4299e1;
      box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
    }
    
    .btn-login {
      background-color: #4299e1;
      color: white;
      padding: 14px;
      width: 100%;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-login:hover {
      background-color: #3182ce;
      transform: translateY(-2px);
    }
    
    .error {
      color: #e53e3e;
      text-align: center;
      margin-bottom: 20px;
      padding: 10px;
      background-color: #fff5f5;
      border-radius: 6px;
    }
    
    .note {
      color: #718096;
      text-align: center;
      margin-top: 20px;
      font-size: 15px;
    }
    
    .link {
      color: #4299e1;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .link:hover {
      color: #3182ce;
      text-decoration: underline;
    }
    
    .divider {
      margin: 20px 0;
      text-align: center;
      position: relative;
    }
    
    .divider::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background-color: #e2e8f0;
    }
    
    .divider-text {
      position: relative;
      background-color: white;
      padding: 0 10px;
      color: #718096;
    }
    
    .remember-me {
      display: flex;
      align-items: center;
      margin-bottom: 20px;
    }
    
    .remember-me input {
      margin-right: 8px;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <div class="login-header">
      <h2>Student Login</h2>
      <p>Please enter your credentials to login</p>
    </div>
    
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    
    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required />
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required />
      </div>
      
      <button type="submit" class="btn-login">Login</button>
      
      <div class="divider">
        <span class="divider-text">or</span>
      </div>
      
      <p class="note">
        Don't have an account? <a href="stud_reg.php" class="link">Register</a>
      </p>
      <p class="note" style="margin-top: 10px;">
        Professor? <a href="prof_login.php" class="link">Professor Portal</a>
      </p>
    </form>
  </div>
</body>
</html>