<?php
// ATP - dbh.inc.php
// This file creates a PDO connection to the lab8_elearn database.

$dsn = 'mysql:host=localhost;dbname=lab8_elearn;charset=utf8mb4';
$dbUser = 'root';
$dbPass = '';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die('Connection failed: ' . $e->getMessage());
}
