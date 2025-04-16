<?php
// ATP - fees.php
// Fees payment portal for course registration using PDO.

session_start();
require_once 'dbh.inc.php';

$course_id = $_GET['course_id'] ?? '';
$student_id = $_GET['student_id'] ?? '';
$bstatus = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Payfees'])) {
        $sql = "INSERT INTO CourseRegistration (CourseID, StudentID) VALUES (:course_id, :student_id)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute(['course_id' => $course_id, 'student_id' => $student_id])) {
            $bstatus = 1;
        }
    }
    if (isset($_POST['Exit'])) {
        header("Location: student_dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- ATP - fees.php -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IITG Fees Portal</title>
    <link rel="stylesheet" href="fees.css?v=2.0">
</head>

<body>
    <h1 class="heading">IIT Guwahati Fees Portal</h1>
    <div id="home" class="tab-content active">
        <h1>Payment Gateway</h1>
        <?php
        if ($bstatus == 0) {
            echo "
        <form method='post'>
            <p style='font-style:italic; color:#555; text-align:center;'>CARD DETAILS</p>
            <input type='text' id='CardNo' name='CardNo' placeholder='Card Number' minlength='19' maxlength='23' pattern='(\d{4} ){2,4}\d{1,4}' title='Enter a valid card number' required>
            <input type='text' class='box1' id='Expiration' name='Expiration' placeholder='MM/YY' pattern='(0[1-9]|1[0-2])\\/\\d{2}' title='Enter expiration in MM/YY format' required>
            <input type='password' class='box2' id='CVV' name='CVV' placeholder='CVV' minlength='3' maxlength='4' pattern='\\d{3,4}' title='CVV must be 3 or 4 digits' required><br>
            <input type='submit' class='button' name='Payfees' value='Pay'>
        </form>";
        } else {
            echo "<p style='font-style:italic; color:green; text-align:center;'>Payment Successful</p>";
            echo "<p style='font-style:italic; color:green; text-align:center;'>Course Successfully Registered</p>";
            echo "<form method='post'><input type='submit' class='exit' name='Exit' value='Exit'></form>";
        }
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                document.getElementById("CardNo").addEventListener("input", function(e) {
                    let val = e.target.value.replace(/\D/g, "");
                    val = val.match(/.{1,4}/g)?.join(" ") || "";
                    e.target.value = val;
                });
                document.getElementById("Expiration").addEventListener("input", function(e) {
                    let val = e.target.value.replace(/\D/g, "");
                    if (val.length > 2) {
                        val = val.substring(0, 2) + "/" + val.substring(2, 4);
                    }
                    e.target.value = val;
                });
                document.getElementById("CVV").addEventListener("input", function(e) {
                    e.target.value = e.target.value.replace(/\D/g, "").substring(0, 4);
                });
            });
        </script>
    </div>
</body>

</html>