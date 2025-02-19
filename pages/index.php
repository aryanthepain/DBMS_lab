<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Portal Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .hero {
            background: url('../images/hero-bg.jpg') no-repeat center center;
            background-size: cover;
            color: #fff;
            padding: 100px 0;
            text-align: center;
        }

        .hero h1 {
            color: green;
        }

        .hero p {
            color: black;
            font-size: 1.2rem;
        }

        .features {
            padding: 50px 0;
        }

        .feature-item {
            padding: 20px;
            text-align: center;
        }

        .footer {
            background: #333;
            color: #fff;
            padding: 20px 0;
            text-align: center;
        }

        .social-links a {
            color: #fff;
            margin: 0 10px;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="hero">
        <div class="container">
            <h1 class="display-4">Welcome to the Exam Portal</h1>
            <p class="lead">Register, book, schedule, take exams and view evaluations—all in one place.</p>
            <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </div>
    <div class="container features">
        <h2 class="text-center mb-4">Key Features</h2>
        <div class="row">
            <div class="col-md-4 feature-item">
                <h4>Student Dashboard</h4>
                <p>View your details, book exams, schedule time slots, take exams, and review evaluations.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Adaptive Exam System</h4>
                <p>Exams with dynamic difficulty and countdown timers ensure fair evaluation.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Detailed Analysis</h4>
                <p>Receive detailed feedback, view metrics such as score percentage, time per question, and more.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Admin Portal</h4>
                <p>Manage exams, create time slots, add questions and administrators, and analyze results—all from one place.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Secure & User-Friendly</h4>
                <p>A secure system with an intuitive interface that works on desktops, tablets, and mobile devices.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Real-Time Updates</h4>
                <p>Get real-time exam status, scheduling updates, and instant feedback on performance.</p>
            </div>
        </div>
    </div>
    <footer class="footer">
        <div class="container">
            <p>Author: Aryan Gupta</p>
            <div class="social-links">
                <a href="https://github.com/aryanthepain" target="_blank">GitHub</a> |
                <a href="https://www.linkedin.com/in/aryanthepain" target="_blank">LinkedIn</a> |
                <a href="https://www.instagram.com/atp.guptaji" target="_blank">Instagram</a> |
                <a href="https://github.com/aryanthepain/DBMS_lab/tree/Lab4" target="_blank">Current Repository</a>
            </div>
            <p>&copy; <?php echo date("Y"); ?> Exam Portal. All rights reserved.</p>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>