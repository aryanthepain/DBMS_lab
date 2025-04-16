<?php
// ATP - logout.php
// Logs the user out by clearing session variables and destroying the session.
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit();
