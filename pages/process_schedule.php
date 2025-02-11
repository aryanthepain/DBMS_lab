<?php
session_start();
require_once '../include/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bookingID = $_POST['bookingID'];
    $selectedSlotID = $_POST['slotID'];

    try {
        $stmt = $pdo->prepare("UPDATE takes_exam SET slot_ID = :slotID WHERE booking_ID = :bookingID");
        $stmt->bindParam(':slotID', $selectedSlotID, PDO::PARAM_INT);
        $stmt->bindParam(':bookingID', $bookingID, PDO::PARAM_INT);
        $stmt->execute();

        echo "<p>Exam scheduled/rescheduled successfully for booking ID: $bookingID with slot ID: $selectedSlotID.</p>";
        echo "<p><a href='dashboard.php'>Return to Dashboard</a></p>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
