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

        .features {
            padding: 50px 0;
        }

        .feature-item {
            padding: 20px;
            text-align: center;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="hero">
        <div class="container">
            <h1 class="display-2">Welcome to the Exam Portal</h1>
            <p class="lead">Register, book, schedule, take exams and view evaluations—all in one place.</p>
            <a href="register.php" class="btn btn-primary btn-lg">Get Started</a>
        </div>
    </div>
    <div class="container features">
        <h2 class="text-center mb-4">Key Features</h2>
        <div class="row">
            <div class="col-md-4 feature-item">
                <h4>Student Dashboard</h4>
                <p>View your personal details, book exams, schedule time slots, and take exams.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Adaptive Exam System</h4>
                <p>Exams with dynamic difficulty adjustments and countdown timers ensure a fair evaluation.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Evaluation & Analysis</h4>
                <p>Receive detailed feedback and view performance metrics including scores, time per question, and percentile rankings.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Admin Dashboard</h4>
                <p>Manage exams, create time slots, add questions, assign administrators, and analyze exam results.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Secure & Easy</h4>
                <p>All exam processes are secured, from registration to evaluation. Access all functionalities with ease.</p>
            </div>
            <div class="col-md-4 feature-item">
                <h4>Responsive Design</h4>
                <p>Works perfectly on desktops, tablets, and mobile devices.</p>
            </div>
        </div>
    </div>
    <footer class="bg-dark text-white text-center p-3">
        <p>&copy; <?php echo date("Y"); ?> Exam Portal. All rights reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>