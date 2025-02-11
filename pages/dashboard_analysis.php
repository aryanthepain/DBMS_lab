<?php
// File: pages/dashboard_analysis.php
session_start();
require_once '../include/dbh.inc.php';

// Example queries (adjust according to your grading and scoring logic)

// Average score (percentage)
$stmt = $pdo->query("SELECT AVG((SELECT COUNT(*) FROM exam_results er WHERE er.booking_ID = t.booking_ID AND er.is_correct = 1) / 
                           (SELECT COUNT(*) FROM exam_results er WHERE er.booking_ID = t.booking_ID)*100) AS avg_score
                     FROM takes_exam t");
$avgScore = $stmt->fetchColumn();
$avgScore = round($avgScore, 2);

// Percentile calculation (for demonstration, using a simple query)
$stmt = $pdo->query("SELECT COUNT(*) FROM takes_exam WHERE end_time IS NOT NULL");
$totalExams = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM takes_exam 
                       WHERE (SELECT COUNT(*) FROM exam_results er WHERE er.booking_ID = takes_exam.booking_ID AND er.is_correct = 1) >= ?
                       AND end_time IS NOT NULL");
$yourScore = 7; // For demonstration, assume candidate answered 7 correctly
$stmt->execute([$yourScore]);
$examsAbove = $stmt->fetchColumn();
$percentile = $totalExams > 0 ? round(($examsAbove / $totalExams) * 100, 2) : 0;

// Average time per question
$stmt = $pdo->query("SELECT AVG(TIMESTAMPDIFF(SECOND, start_time, end_time)) FROM exam_results");
$avgTime = round($stmt->fetchColumn(), 2);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .chart-container {
            width: 80%;
            margin: auto;
        }
    </style>
</head>

<body>
    <?php include 'navbar.php'; ?>
    <div class="container mt-5">
        <h2>Strengths & Weaknesses Dashboard</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">Average Score</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $avgScore; ?>%</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning mb-3">
                    <div class="card-header">Percentile</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $percentile; ?>th</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger mb-3">
                    <div class="card-header">Avg. Time per Question</div>
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $avgTime; ?> sec</h5>
                    </div>
                </div>
            </div>
        </div>
        <div class="chart-container">
            <!-- Placeholder for a dynamic chart (you can integrate Chart.js here) -->
            <img src="https://via.placeholder.com/800x400?text=Dashboard+Chart" class="img-fluid" alt="Dashboard Chart">
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>