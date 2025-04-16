<?php

/**
 * File: prof_reg.php
 * Author: Aryan Gupta
 *
 * Instructor registration page for eLearn using a PDO connection.
 */

session_start();
require_once 'dbh.inc.php'; // This file creates a PDO connection stored in $pdo

// Enable error reporting for debugging purposes.
error_reporting(E_ALL);
ini_set('display_errors', 1);

$successMessage = "";
$errorMessage   = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Retrieve and sanitize the form inputs.
  $instructorID = trim($_POST['id']);
  $fullName     = trim($_POST['name']);
  $phoneNumber  = trim($_POST['phone']);
  $email        = trim($_POST['email']);
  $username     = trim($_POST['username']);
  $passwordRaw  = $_POST['password'];

  // For demonstration purposes, we store the password as plain text.
  // (In production, always hash passwords before storing.)
  $storedPassword = $passwordRaw;

  try {
    // Prepare the PDO statement to insert the new instructor.
    $sql  = "INSERT INTO Instructors (ID, Name, PhoneNo, Email, Username, Password) VALUES (?,?,?,?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$instructorID, $fullName, $phoneNumber, $email, $username, $storedPassword]);

    $successMessage = "Registration successful! You can now <a href='prof_login.php'>log in</a>.";
  } catch (PDOException $e) {
    // Check for duplicate entry (SQLSTATE 23000 indicates an integrity constraint violation)
    if ($e->getCode() == 23000) {
      $errorMessage = "Username / ID already exists. Please choose another.";
    } else {
      $errorMessage = "Registration failed: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Instructor Registration - eLearn</title>
  <style>
    /* Global Reset */
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
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    /* Website Heading */
    .site-heading {
      text-align: center;
      margin-bottom: 20px;
    }

    .site-heading h1 {
      font-size: 38px;
      color: #333;
    }

    /* Registration Container */
    .reg-container {
      width: 380px;
      background: #ffffff;
      padding: 35px 30px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .reg-header {
      text-align: center;
      margin-bottom: 25px;
    }

    .reg-header h2 {
      font-size: 26px;
      color: #333;
      margin-bottom: 5px;
    }

    .reg-header p {
      font-size: 14px;
      color: #666;
    }

    .form-group {
      margin-bottom: 18px;
    }

    .form-group label {
      display: block;
      font-weight: bold;
      font-size: 14px;
      margin-bottom: 6px;
      color: #444;
    }

    .form-control {
      width: 100%;
      padding: 12px 10px;
      font-size: 15px;
      border: 1px solid #ccc;
      border-radius: 6px;
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
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
      font-weight: bold;
      transition: background-color 0.3s ease, transform 0.2s ease;
    }

    .btn-submit:hover {
      background-color: #478ac9;
      transform: translateY(-2px);
    }

    .message {
      text-align: center;
      margin-bottom: 20px;
      padding: 12px;
      border-radius: 6px;
      font-size: 15px;
    }

    .success {
      color: #2e7d32;
      background-color: #e8f5e9;
      border: 1px solid #a5d6a7;
    }

    .error {
      color: #c62828;
      background-color: #ffebee;
      border: 1px solid #ef9a9a;
    }

    .note {
      color: #666;
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }

    .link {
      color: #5a9bd5;
      text-decoration: none;
      font-weight: bold;
      transition: color 0.2s ease;
    }

    .link:hover {
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
      right: 0;
      height: 1px;
      background-color: #ddd;
      z-index: -1;
    }

    .divider span {
      position: relative;
      background: #ffffff;
      padding: 0 10px;
      color: #888;
    }
  </style>
</head>

<body>
  <!-- Website Heading -->
  <div class="site-heading">
    <h1>eLearn</h1>
  </div>

  <div class="reg-container">
    <div class="reg-header">
      <h2>Instructor Registration</h2>
      <p>Create your account</p>
    </div>

    <?php
    if ($successMessage) {
      echo "<div class='message success'>$successMessage</div>";
    }
    if ($errorMessage) {
      echo "<div class='message error'>$errorMessage</div>";
    }
    ?>

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
      <button type="submit" class="btn-submit">Register</button>

      <div class="divider">
        <span>or</span>
      </div>

      <p class="note">
        Already have an account? <a href="prof_login.php" class="link">Login</a>
      </p>
    </form>
  </div>
</body>

</html>