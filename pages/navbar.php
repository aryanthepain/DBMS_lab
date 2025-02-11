<?php
// Start session if not already started.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container">
        <a class="navbar-brand" href="index.php">Exam Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <?php if (isset($_SESSION['admin'])): ?>
                    <!-- Admin-specific links -->
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="exam_registration.php">Exam Registration</a></li>
                    <li class="nav-item"><a class="nav-link" href="create_timeslot.php">Create Time Slot</a></li>
                    <li class="nav-item"><a class="nav-link" href="book_exam_slot.php">Book Exam Slot</a></li>
                    <li class="nav-item"><a class="nav-link" href="manage_questions.php">Manage Questions</a></li>
                    <li class="nav-item"><a class="nav-link" href="feedback.php">Provide Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard_analysis.php">Analysis Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php elseif (isset($_SESSION['roll'])): ?>
                    <!-- Student-specific links -->
                    <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="exam_registration.php">Exam Registration</a></li>
                    <li class="nav-item"><a class="nav-link" href="booking.php">Booking & Fees</a></li>
                    <li class="nav-item"><a class="nav-link" href="schedule.php">Schedule</a></li>
                    <li class="nav-item"><a class="nav-link" href="exam_portal.php">Take Exam</a></li>
                    <li class="nav-item"><a class="nav-link" href="evaluation.php">Evaluation</a></li>
                    <li class="nav-item"><a class="nav-link" href="dashboard_analysis.php">Analysis Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="questions_feedback.php">Questions Feedback</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                <?php else: ?>
                    <!-- Public links -->
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Student Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>