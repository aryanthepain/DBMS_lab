<?php
session_start();
require_once '../include/dbh.inc.php';

// Ensure the student is logged in.
if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}

$roll = $_SESSION['roll'];

// Check if a specific booking is selected via GET parameter
if (isset($_GET['bookingID'])) {
    // A booking has been selected: display the scheduling form for that exam.
    $bookingID = $_GET['bookingID'];

    // Retrieve the booking details from takes_exam
    $stmt = $pdo->prepare("SELECT booking_ID, Exam_ID, slot_ID FROM takes_exam WHERE booking_ID = :bookingID");
    $stmt->bindParam(':bookingID', $bookingID, PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$booking) {
        die("Booking not found.");
    }

    $examID = $booking['Exam_ID'];
    $currentSlotID = $booking['slot_ID'];

    // Retrieve exam details (optional, for display)
    $stmt = $pdo->prepare("SELECT name FROM exam WHERE Exam_ID = :examID");
    $stmt->bindParam(':examID', $examID, PDO::PARAM_INT);
    $stmt->execute();
    $examData = $stmt->fetch(PDO::FETCH_ASSOC);
    $examName = $examData ? $examData['name'] : "Exam";

    // Retrieve available slots for this exam from available_on_slot joined with slot
    $stmt = $pdo->prepare("
        SELECT aos.slot_ID, s.start_time, s.duration 
        FROM available_on_slot aos 
        JOIN slot s ON aos.slot_ID = s.slot_ID 
        WHERE aos.Exam_ID = :examID
    ");
    $stmt->bindParam(':examID', $examID, PDO::PARAM_INT);
    $stmt->execute();
    $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Schedule/Reschedule Exam Slot</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
        <?php include 'navbar.php'; ?>
        <div class="container mt-5">
            <h2>Schedule/Reschedule: <?php echo htmlspecialchars($examName); ?></h2>
            <p>Booking ID: <?php echo htmlspecialchars($bookingID); ?></p>
            <?php if (empty($slots)): ?>
                <p>No available time slots for this exam. Please contact the admin.</p>
                <p><a href="schedule.php" class="btn btn-secondary">Back to Exam Bookings</a></p>
            <?php else: ?>
                <form action="process_schedule.php" method="POST">
                    <!-- Pass the booking ID as hidden -->
                    <input type="hidden" name="bookingID" value="<?php echo htmlspecialchars($bookingID); ?>">
                    <div class="mb-3">
                        <label for="slotID" class="form-label">Select a Time Slot</label>
                        <select class="form-select" id="slotID" name="slotID" required>
                            <option value="">Choose a time slot</option>
                            <?php foreach ($slots as $slot): ?>
                                <option value="<?php echo $slot['slot_ID']; ?>" <?php if ($currentSlotID == $slot['slot_ID']) echo "selected"; ?>>
                                    <?php echo "Slot " . $slot['slot_ID'] . " - " . $slot['start_time'] . " (" . $slot['duration'] . " min)"; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Schedule/Reschedule Exam</button>
                    <a href="schedule.php" class="btn btn-secondary ms-2">Back to Exam Bookings</a>
                </form>
            <?php endif; ?>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
} else {
    // No specific booking selected: display the list of exam bookings.
    $stmt = $pdo->prepare("
        SELECT te.booking_ID, te.Exam_ID, te.slot_ID, e.name AS exam_name 
        FROM takes_exam te 
        JOIN exam e ON te.Exam_ID = e.Exam_ID
        WHERE te.Roll_number = :roll
        ORDER BY te.booking_ID DESC
    ");
    $stmt->bindParam(':roll', $roll, PDO::PARAM_INT);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Select Exam to Schedule</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>
        <?php include 'navbar.php'; ?>
        <div class="container mt-5">
            <h2>Select an Exam to Schedule/Reschedule</h2>
            <?php if (count($bookings) == 0): ?>
                <p>You have not booked any exams yet. Please book an exam first.</p>
            <?php else: ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Exam Name</th>
                            <th>Current Slot</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['booking_ID']); ?></td>
                                <td><?php echo htmlspecialchars($booking['exam_name']); ?></td>
                                <td>
                                    <?php
                                    if ($booking['slot_ID']) {
                                        echo "Slot " . htmlspecialchars($booking['slot_ID']);
                                    } else {
                                        echo "Not scheduled";
                                    }
                                    ?>
                                </td>
                                <td>
                                    <a href="schedule.php?bookingID=<?php echo $booking['booking_ID']; ?>" class="btn btn-primary">Schedule/Reschedule</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
<?php
}
?>