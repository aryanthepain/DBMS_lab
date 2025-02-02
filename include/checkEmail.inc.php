<?php
require_once '../include/dbh.inc.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = $_POST['email'];

    $stmt = $pdo->prepare("SELECT Roll_number FROM student_password WHERE Roll_number = ?");
    $stmt->bindParam("s", $roll);
    $stmt->execute();
    $stmt->fetchAll();

    if ($stmt->rowCount() > 0) {
        echo 'exists';
    } else {
        echo 'not exists';
    }

    $stmt = null;
    $pdo = null;
}
