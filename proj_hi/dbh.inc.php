<?php
// ATP - dbh.inc.php
// This file creates a PDO connection to the DA215_project2 database.

$dsn = 'mysql:host=localhost;dbname=da215_project2;charset=utf8mb4';
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
