<?php
session_start();

// Unset admin session variable
unset($_SESSION['admin']);

// Destroy the session
session_destroy();

// Redirect to the login page
header("Location: adminlogin.php");
exit();
?>