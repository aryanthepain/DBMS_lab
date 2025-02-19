<?php
// author: aryanthepain
// file: pages/process_booking.php
session_start();
require_once '../include/dbh.inc.php';
require_once '../include/displayAlert.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['roll'])) {
        die("You must be logged in to book an exam.");
    }
    $roll = $_SESSION['roll'];
    $examID = $_POST['examID'];
    $transactionID = $_POST['transactionID'];

    try {
        $stmt = $pdo->prepare("INSERT INTO takes_exam (Roll_number, Exam_ID, transaction_ID) VALUES (:roll, :examID, :transID)");
        $stmt->bindParam(':roll', $roll, PDO::PARAM_INT);
        $stmt->bindParam(':examID', $examID, PDO::PARAM_INT);
        $stmt->bindParam(':transID', $transactionID, PDO::PARAM_STR);
        $stmt->execute();
        $bookingID = $pdo->lastInsertId();
        $_SESSION['booking_ID'] = $bookingID;

        $alert = 'Exam booked successfully. Booking ID:' . $bookingID . '\nReturn to Dashboard';
        display_alert($alert, 'dashboard.php');
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
