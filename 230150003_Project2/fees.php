<?php
    // Connecting SQL Database with PHP
    session_start();
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "DA215_Project2";
    $conn = "";
    $conn = mysqli_connect($db_server,$db_user,$db_pass,$db_name);
    if(!$conn){
       die("Connection to the Database failed due to " . mysqli_connect_error()); //Error Handling
    }

    $course_id = $_GET['course_id'];
    $student_id = $_GET['student_id'];
    $bstatus=0;

    if($_SERVER['REQUEST_METHOD']=='POST'){
        if(isset($_POST['Payfees'])){
            $sql = "INSERT INTO CourseRegistration (CourseID, StudentID) VALUES (?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ss", $course_id, $student_id);
            $stmt->execute();
            $bstatus=1;
        }
        if(isset($_POST['Exit'])){
            header("Location: student_dashboard.php");
            exit();
        }
    }
    $conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="fees.css?v=2.0">    
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IITG Fees Portal</title>
</head>
<body>
    <h1 class="heading">IIT Guwahati Fees Portal</h1>

    <div id="home" class="tab-content active">

        <h1>Payment Gateway</h1>
        <?php 
            if($bstatus==0){
                echo "
                <form method='post'>
                    <p style='font-style:oblique; color:grey; text-align:center;'>CARD DETAILS</p>
                    <input type='text' id='CardNo' name='CardNo' placeholder='Card Number' 
                        minlength='19' maxlength='23' 
                        pattern='(\d{4} ){2,4}\d{1,4}' 
                        title='Enter a valid card number (13-19 digits with spaces)' required>
                    <input type='text' class='box1' id='Expiration' name='Expiration' placeholder='MM/YY' 
                        pattern='(0[1-9]|1[0-2])\\/\\d{2}' 
                        title='Enter expiration in MM/YY format (01-12/YY)' required>
                    <input type='password' class='box2' id='CVV' name='CVV' placeholder='CVV' 
                        minlength='3' maxlength='4' 
                        pattern='\\d{3,4}' 
                        title='CVV must be 3 or 4 digits' required><br>
                    <input type='submit' class='button' name='Payfees' value='Pay'>
                </form>";
        }else{
            echo "<p style='font-style:oblique; color:green; text-align:center;'>Payment Successful</p>";
            echo "<p style='font-style:oblique; color:green; text-align:center;'>Course Successfully Registered</p>";
            echo "<form method='post'><input type='submit' class='exit' name='Exit' value='Exit'></form>";
        }
        ?>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            //Card Number Auto-Spacing (Every 4 Digits)
            document.getElementById("CardNo").addEventListener("input", function (e) {
                let val = e.target.value.replace(/\D/g, ""); 
                val = val.match(/.{1,4}/g)?.join(" ") || ""; 
                e.target.value = val;
            });
            //Expiration Date Auto-Formatting (MM/YY)
            document.getElementById("Expiration").addEventListener("input", function (e) {
                let val = e.target.value.replace(/\D/g, ""); 
                if (val.length > 2) {
                    val = val.substring(0, 2) + "/" + val.substring(2, 4);
                }
                e.target.value = val;
            });
            document.getElementById("CVV").addEventListener("input", function (e) {
                    e.target.value = e.target.value.replace(/\D/g, "").substring(0, 4); 
            });
        });
    </script> 
    </div>
</body>
</html>