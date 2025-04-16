<?php
// ATP - prof_login.php
// Instructor login page using PDO.
session_start();
require_once 'dbh.inc.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username']);
  $password_input = $_POST['password'];

  $sql = "SELECT * FROM Instructors WHERE Username = :username";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['username' => $username]);

  if ($stmt->rowCount() === 1) {
    $instructor = $stmt->fetch();
    if ($password_input === $instructor['Password']) {
      $_SESSION['instructor_id'] = $instructor['ID'];
      $_SESSION['instructor_name'] = $instructor['Name'];
      header("Location: instructor_dashboard.php");
      exit();
    } else {
      $error = "Invalid password.";
    }
  } else {
    $error = "Instructor not found.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- ATP - prof_login.php -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Instructor Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2c7873 0%, #a3c9a8 100%);
      font-family: 'Segoe UI', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .login-box {
      width: 400px;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .login-header h2 {
      color: #2c7873;
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .login-header p {
      color: #555;
      font-size: 16px;
    }

    .form-control {
      border-radius: 6px;
      padding: 12px;
    }

    .btn-login {
      background-color: #2c7873;
      color: white;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn-login:hover {
      background-color: #23635b;
    }

    .error {
      background-color: #ffe6e6;
      color: #cc0000;
      padding: 10px;
      text-align: center;
      margin-bottom: 15px;
      border-radius: 6px;
    }

    .note {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
      color: #555;
    }

    .link {
      color: #2c7873;
      text-decoration: none;
    }

    .link:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="login-box">
    <div class="login-header">
      <h2>Instructor Login</h2>
      <p>Please enter your credentials to login</p>
    </div>
    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Enter your username" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn-login">Login</button>
      <div class="divider"><span class="divider-text">or</span></div>
      <p class="note">Don't have an account? <a href="prof_reg.php" class="link">Register</a></p>
      <p class="note">Student? <a href="index.php" class="link">Student Portal</a></p>
    </form>
  </div>
</body>

</html>