<?php
//Destroying all the Session variables
session_start();
session_unset();
session_destroy();
// Redirect to login.php
header("Location: index.php");
exit();
