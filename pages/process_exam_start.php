<?php
// author: aryanthepain
// File: pages/process_exam_start.php
session_start();
require_once '../include/dbh.inc.php';

if (!isset($_SESSION['roll'])) {
    header("Location: login.php");
    exit();
}
$roll = $_SESSION['roll'];

// Ensure booking_ID and exam_ID are provided.
if (!isset($_POST['booking_ID']) || !isset($_POST['exam_ID'])) {
    die("No exam booking selected. Please go back and choose an exam.");
}
$bookingID = $_POST['booking_ID'];
$examID = $_POST['exam_ID'];
$_SESSION['booking_ID'] = $bookingID;
$_SESSION['exam_ID'] = $examID;

// Default base64 image string (replace with your actual default image string)
$defaultPhoto = 'iVBORw0KGgoAAAANSUhEUgAAAMwAAADACAMAAAB/Pny7AAAAMFBMVEXk5ueutLeor7La3d/n6erh4+SrsbXT1ti7wMPq7O2zubzKztDHy83Bxsi4vcDd4OHJo5BXAAAFEElEQVR4nO2cXZucIAxGUQIoCv7/f1t02t35XoHXSdxyrtq9mvMAgUSIUo1Go9FoNBqNRqPRaDQajUaj0Wg0Go2GbIj7ByCghFk2jFn/w/2Dikke2k1zGDZCmCenzTl9yLh5iN5e4+MQ3Ol8aNQh+s7a7o5NyJ1Jh0a3mtyL/PPpungenVH7Fx7fRD2eQYeW4dWYXNMPywls3Mv5dTfb+on7p/4ALaHfpXKCwSHt97skm07LtaG9U+xrqnWO+ze/ZMoyuehIXTgFLgmZNmUuIm2o1EWgTVr75ThZMS3F5Lw4doMXtt9k7JWP9AP3z7/B1bgkG0nLRleprDZyJhrFigWzYaMUGaqcZNvQSIloVK2S8NwWF2iqnWQbk4yhQaikoTHcHgo2MDKGhn6uXuzCRm6TykPZLfwBjQaUiw3sMiaiZLqomV2As4x/nlEAxbKEnXldlNlVvtwpMyysLrSjrLwfz1tGq8j8n8G7aGDb/wZzEc0A1/+607Cez/Z9vtgtwxoBSFfnmDdEzgiADWYJVhmHlbGc4Qx6mOmYKwHkoEuGWwbqkmT4XA4YmV8l09YMSuZXhebftGlWfWN6hDehwR40u8iaaoJTgIG3RIuVmVll0Jkmb9qMDWfMhTONK2gy52ZqLTUD62a8JYAkM8NcuJcMNnH23LVmpXCfNJh3GQWdZzP7wNCCcmGuNF9sUPOM/8PZGgJAwZl/+av15gzExUtwSXlA/dWZFMsErJiVEXEKiCO3xj8At5q4Fb6gqdbGTmIGRqnKUoAVsvov1J7QuK8z3FJXdJZ033SlohhgA/ePv6e86MR9l+EZS+HnTRvlXAP+ZikKAlba3fkLZEpc5L7UypfxRqqLotzNM0p+SUsh4wmdFZGPvYHc7uzGRhHp2DtomXcNThoWkWHsHjc8Pp2/V7GD3BeaN5BxQ/9Ox/aDkxvF7kk6oX/hY3sbNHu5L5PFxUcf2/dxkncU2wEpF5JQ/7eBRvpXF040vR6gcVTaTStOm/EU3RneQpSUxnP3nFHKrCxXbH8w6jxe6y9NCtpd2udE/9V5xvu4ttKZnNN6ka+09TJKFmGI23p/1namX//uY1gX0SLViLZmRnOI3j6ReCbV+SEN06LE+RAlkWGbUj953Ch16xhpST40LlMSyeyfcSPklIiKJo1m8s+6MuUIpYwzOPZNiEYN+0DrZ8PpQ2buEN9m/pIO03zVDZNOxjiVi07kSXP0+5yllN67D+cHpHQ4RGXTie6jSYLel+eXknLqT9UHSM/Ya6ZPbLrPlAhITdgHQK90wvGRbdTx0Bl2peOnY085RIfPsCub7tCPHbQgLzHu0TnuOl12LzaATjimbS2pGbzf76E/5hJqTutCIEd02iMdWVy6Axo5kftcFHu0maELh2Hp39ggwwCzCzSowd8vMdoIcIHZoJ9iFQLpFkKl10jQIF4KmIFrf7kH8O6R4wzzHFt9JR38RLaKyg6VVN2GEUpf9fQB+W4JQV8x0eqv+KKpuWUra1y69QRdagO5Fo+msD4Ie0uCxA5lH3LGz1Yv9lK0daKbfYAo61Jb3x33GEoeQIo4+D+j6H469HE8koIjGvQFNpbsvQbcHw9J/ktbbD8JMJkhgATPsuyqILrVD5TsvgHizsvXZMYzZONSPHnVAHivPyx5i0bkgfmbzEUD6PR/IJk7DbQ3Dp6YIyN6y1zJyTfN0Msm68WtFs7ZHkY0Go1Go9Fo/H/8AVoJTYWJQwYkAAAAAElFTkSuQmCC';
$photoData = $defaultPhoto;

if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
    $photoData = file_get_contents($_FILES['photo']['tmp_name']);
}

// Update student's photo in the database.
$stmt = $pdo->prepare("UPDATE students SET photo = ? WHERE Roll_number = ?");
$stmt->execute([$photoData, $roll]);

header("Location: questions.php");
exit();
