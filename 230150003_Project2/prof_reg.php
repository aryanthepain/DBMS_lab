<?php

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = trim($_POST['id']);
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password_raw = $_POST['password'];
    $hashed_password = $password_raw;

    $stmt = $conn->prepare("INSERT INTO Instructors (`ID`,`Name`, PhoneNo, Email, Username, `Password`) VALUES (?,?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $id,$name, $phone, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        $success = "Registration successful! You can now <a href='prof_login.php'>log in</a>.";
    } else {
        if ($conn->errno == 1062) {
            $error = "Username / ID already exists. Please choose another.";
        } else {
            $error = "Registration failed: " . $conn->error;
        }
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
  <title>Student Registration</title>
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }
    
    body {
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }
    
    .register-box {
      width: 450px;
      background: white;
      padding: 40px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
      margin: 40px auto;
    }
    
    .register-header {
      text-align: center;
      margin-bottom: 30px;
    }
    
    .register-header h2 {
      color: #2d3748;
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 8px;
    }
    
    .register-header p {
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
      border-color: #48bb78;
      box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.2);
    }
    
    .btn-register {
      background-color: #48bb78;
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
    
    .btn-register:hover {
      background-color: #38a169;
      transform: translateY(-2px);
    }
    
    .message {
      text-align: center;
      margin-bottom: 20px;
      padding: 12px;
      border-radius: 6px;
    }
    
    .success {
      color: #2f855a;
      background-color: #f0fff4;
      border: 1px solid #c6f6d5;
    }
    
    .error {
      color: #e53e3e;
      background-color: #fff5f5;
      border: 1px solid #fed7d7;
    }
    
    .note {
      color: #718096;
      text-align: center;
      margin-top: 20px;
      font-size: 15px;
    }
    
    .link {
      color: #48bb78;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    
    .link:hover {
      color: #38a169;
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
    
    @media (max-width: 500px) {
      .register-box {
        width: 100%;
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>
  <div class="register-box">
    <div class="register-header">
      <h2>Instructor Registration</h2>
      <p>Create your account</p>
    </div>
    
    <?php if ($success) echo "<div class='message success'>$success</div>"; ?>
    <?php if ($error) echo "<div class='message error'>$error</div>"; ?>
    
    <form method="POST" action="">
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required />
      </div>
      <div class="form-group">
        <label for="id">Instructor ID</label>
        <input type="text" id="id" name="id" class="form-control" placeholder="Enter your Instructor ID" required />
      </div>
      
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required />
      </div>
      
      <div class="form-group">
        <label for="email">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required />
      </div>
      
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" required />
      </div>
      
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Create a strong password" required />
      </div>
      
      <button type="submit" class="btn-register">Register</button>
      
      <div class="divider">
        <span class="divider-text">or</span>
      </div>
      
      <p class="note">
        Already have an account? <a href="prof_login.php" class="link">Login</a>
      </p>
    </form>
  </div>
</body>
</html>