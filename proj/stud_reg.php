<?php
// ATP - stud_reg.php
// Student registration page using PDO.
session_start();
require_once 'dbh.inc.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $id = trim($_POST['id']);
  $name = trim($_POST['name']);
  $phone = trim($_POST['phone']);
  $email = trim($_POST['email']);
  $username = trim($_POST['username']);
  $password_raw = $_POST['password'];
  // Use plain text here; in production hash the password.
  $hashed_password = $password_raw;

  $sql = "INSERT INTO Students (ID, Name, PhoneNo, Email, Username, Password) VALUES (:id, :name, :phone, :email, :username, :password)";
  $stmt = $pdo->prepare($sql);
  try {
    $stmt->execute([
      'id' => $id,
      'name' => $name,
      'phone' => $phone,
      'email' => $email,
      'username' => $username,
      'password' => $hashed_password
    ]);
    $success = "Registration successful! You can now <a href='index.php'>log in</a>.";
  } catch (PDOException $e) {
    if ($e->getCode() == 23000) {
      $error = "Username / ID already exists. Please choose another.";
    } else {
      $error = "Registration failed: " . $e->getMessage();
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <!-- ATP - stud_reg.php -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #2c7873 0%, #a3c9a8 100%);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }

    .register-box {
      width: 450px;
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }

    .register-header h2 {
      color: #2c7873;
      font-size: 28px;
      font-weight: 600;
      margin-bottom: 10px;
      text-align: center;
    }

    .register-header p {
      color: #555;
      font-size: 16px;
      text-align: center;
    }

    .form-control {
      border-radius: 6px;
      padding: 12px;
      margin-bottom: 15px;
    }

    .btn-register {
      background-color: #2c7873;
      color: #fff;
      padding: 12px;
      width: 100%;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.3s;
    }

    .btn-register:hover {
      background-color: #23635b;
    }

    .message {
      text-align: center;
      margin-bottom: 15px;
      padding: 10px;
      border-radius: 6px;
    }

    .success {
      background-color: #e6ffed;
      color: #2f855a;
      border: 1px solid #c6f6d5;
    }

    .error {
      background-color: #ffe6e6;
      color: #cc0000;
      border: 1px solid #fed7d7;
    }

    .note {
      text-align: center;
      margin-top: 20px;
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

    .divider {
      position: relative;
      margin: 20px 0;
      text-align: center;
    }

    .divider::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 0;
      right: 0;
      height: 1px;
      background: #ddd;
    }

    .divider-text {
      position: relative;
      background: #fff;
      padding: 0 10px;
      color: #888;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <div class="register-box">
    <div class="register-header">
      <h2>Student Registration</h2>
      <p>Create your student account</p>
    </div>
    <?php
    if ($success) echo "<div class='message success'>$success</div>";
    if ($error) echo "<div class='message error'>$error</div>";
    ?>
    <form method="POST" action="">
      <input type="hidden" name="id" value="<?php echo uniqid('S'); ?>">
      <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
      </div>
      <div class="mb-3">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="text" id="phone" name="phone" class="form-control" placeholder="Enter your phone number" required>
      </div>
      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
      </div>
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="Choose a username" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="Create a strong password" required>
      </div>
      <button type="submit" class="btn-register">Register</button>
      <div class="divider"><span class="divider-text">or</span></div>
      <p class="note">Already have an account? <a href="index.php" class="link">Login</a></p>
    </form>
  </div>
</body>

</html>