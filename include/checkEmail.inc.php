<?php
include '../database/db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $roll = $_POST['email'];

    $stmt = $pdo->prepare("SELECT email FROM userdata WHERE email = ?");
    $stmt->bindParam("s", $roll);
    $stmt->execute();
    $stmt->fetchAll();

    if ($stmt->rowCount() > 0) {
        echo 'exists';
    } else {
        echo 'not exists';
    }

    $stmt->close();
    $pdo->close();
}
