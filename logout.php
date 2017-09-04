<?php
// Destroy the session to logout the user and send them back to the main page
session_start();
session_destroy();
header('Location: index.php');
die();
?>