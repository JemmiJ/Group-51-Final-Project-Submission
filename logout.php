<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy the session
session_destroy();

// Redirect to login page or wherever you want after logout
header("Location: Login.php");
exit;
?>
