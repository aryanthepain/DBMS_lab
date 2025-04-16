 <?php
  /**
   * File: prof_login.php
   * Author: Aryan Gupta
   *
   * Instructor login page for eLearn using a PDO connection.
   */

  session_start();
  require_once 'dbh.inc.php'; // This file creates a PDO connection stored in $pdo

  $errorMessage = "";

  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usernameInput = trim($_POST['username']);
    $passwordInput = $_POST['password'];

    // Prepare statement to retrieve instructor data using PDO
    $sql  = "SELECT * FROM Instructors WHERE Username = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$usernameInput]);

    if ($stmt->rowCount() === 1) {
      $instructor = $stmt->fetch();
      // Direct plaintext password comparison (for demonstration; use hashing in production)
      if ($passwordInput === $instructor['Password']) {
        $_SESSION['instructor_id']   = $instructor['ID'];
        $_SESSION['instructor_name'] = $instructor['Name'];
        header("Location: instructor_dashboard.php");
        exit();
      } else {
        $errorMessage = "Invalid password.";
      }
    } else {
      $errorMessage = "Instructor not found.";
    }
  }
  ?>

 <!DOCTYPE html>
 <html lang="en">

 <head>
   <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>Instructor Login - eLearn</title>
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

     /* Site Heading */
     .site-heading {
       text-align: center;
       margin-bottom: 20px;
     }

     .site-heading h1 {
       font-size: 38px;
       color: #333;
     }

     /* Login Container */
     .login-box {
       width: 400px;
       background: #ffffff;
       padding: 40px;
       box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
       border-radius: 12px;
     }

     .login-header {
       text-align: center;
       margin-bottom: 30px;
     }

     .login-header h2 {
       font-size: 28px;
       color: #333;
       font-weight: 600;
       margin-bottom: 8px;
     }

     .login-header p {
       font-size: 16px;
       color: #666;
     }

     .form-group {
       margin-bottom: 20px;
     }

     .form-group label {
       display: block;
       margin-bottom: 8px;
       font-weight: 500;
       color: #444;
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
       font-size: 15px;
       text-align: center;
       margin-top: 20px;
       color: #666;
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

     .divider span {
       position: relative;
       background-color: #ffffff;
       padding: 0 10px;
       color: #718096;
     }
   </style>
 </head>

 <body>
   <!-- Website Heading -->
   <div class="site-heading">
     <h1>eLearn</h1>
   </div>

   <div class="login-box">
     <div class="login-header">
       <h2>Instructor Login</h2>
       <p>Please enter your credentials to login</p>
     </div>

     <?php if (!empty($errorMessage)) echo "<div class='error'>$errorMessage</div>"; ?>

     <form method="POST" action="">
       <div class="form-group">
         <label for="username">Username</label>
         <input
           type="text"
           id="username"
           name="username"
           class="form-control"
           placeholder="Enter your username"
           required />
       </div>

       <div class="form-group">
         <label for="password">Password</label>
         <input
           type="password"
           id="password"
           name="password"
           class="form-control"
           placeholder="Enter your password"
           required />
       </div>

       <button type="submit" class="btn-login">Login</button>

       <div class="divider">
         <span>or</span>
       </div>

       <p class="note">
         Don't have an account? <a href="prof_reg.php" class="link">Register</a>
       </p>
       <p class="note" style="margin-top: 10px;">
         Student? <a href="index.php" class="link">Student Portal</a>
       </p>
     </form>
   </div>
 </body>

 </html>