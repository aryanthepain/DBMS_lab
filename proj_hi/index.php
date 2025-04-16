<?php

/**
 * File: index.php
 * Author: ATP
 *
 * Student login page that authenticates students using a PDO connection.
 */

session_start();
require_once 'dbh.inc.php'; // Include the PDO connection from dbh.inc.php

// Initialize error message variable.
$errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $usernameInput = trim($_POST['username']);
  $passwordInput = $_POST['password'];

  // Prepare statement to retrieve student data.
  $sql = "SELECT * FROM Students WHERE Username = ?";
  $stmt = $dbConnection->prepare($sql);

  if ($stmt->execute([$usernameInput])) {
    if ($stmt->rowCount() === 1) {
      $student = $stmt->fetch();
      // Compare plaintext passwords; in production, be sure to hash passwords.
      if ($passwordInput === $student['Password']) {
        $_SESSION['student_id']   = $student['ID'];
        $_SESSION['student_name'] = $student['Name'];
        header("Location: student_dashboard.php");
        exit();
      } else {
        $errorMessage = "Invalid password.";
      }
    } else {
      $errorMessage = "Student not found.";
    }
  } else {
    $errorMessage = "Database error.";
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Student Login</title>
  <style>
    /* Reset box-sizing and margin */
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: linear-gradient(135deg, #eef2f3 0%, #8e9eab 100%);
      font-family: Arial, sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .login-container {
      width: 380px;
      background: #ffffff;
      padding: 35px 30px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      position: relative;
    }

    .login-header {
      text-align: center;
      margin-bottom: 25px;
    }

    .login-header h2 {
      font-size: 26px;
      color: #333;
      margin-bottom: 5px;
    }

    .login-header p {
      font-size: 14px;
      color: #666;
    }

    .form-group {
      margin-bottom: 18px;
    }

    label {
      display: block;
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 6px;
      color: #444;
    }

    .form-input {
      width: 100%;
      padding: 12px 10px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      transition: border-color 0.3s ease;
    }

    .form-input:focus {
      outline: none;
      border-color: #5a9bd5;
      box-shadow: 0 0 0 3px rgba(90, 155, 213, 0.2);
    }

    .btn-submit {
      width: 100%;
      padding: 12px;
      background-color: #5a9bd5;
      border: none;
      border-radius: 6px;
      font-size: 15px;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-submit:hover {
      background-color: #478ac9;
      transform: translateY(-2px);
    }

    .alert {
      background: #fdecea;
      border: 1px solid #f5c2c0;
      padding: 10px;
      border-radius: 6px;
      color: #cc1f1a;
      text-align: center;
      margin-bottom: 15px;
    }

    .info {
      font-size: 13px;
      text-align: center;
      color: #555;
    }

    .info a {
      color: #5a9bd5;
      text-decoration: none;
      font-weight: bold;
      transition: color 0.2s ease;
    }

    .info a:hover {
      color: #478ac9;
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
      width: 100%;
      height: 1px;
      background: #ddd;
      z-index: -1;
    }

    .divider span {
      background: #ffffff;
      padding: 0 10px;
      color: #888;
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="login-header">
      <h2>Student Login</h2>
      <p>Please enter your details below</p>
    </div>

    <?php if (!empty($errorMessage)): ?>
      <div class="alert">
        <?= htmlspecialchars($errorMessage) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" name="username" id="username" class="form-input" placeholder="Enter your username" required />
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" id="password" class="form-input" placeholder="Enter your password" required />
      </div>

      <button type="submit" class="btn-submit">Log In</button>

      <div class="divider">
        <span>or</span>
      </div>

      <p class="info">Don't have an account? <a href="stud_reg.php">Register</a></p>
      <p class="info" style="margin-top: 8px;">Professor? <a href="prof_login.php">Access Portal</a></p>
    </form>
  </div>
</body>

</html>