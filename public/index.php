<?php
session_start();
if (!isset($_SESSION['user']) && !isset($_COOKIE['user'])) {
    header('Location: login.php');
    exit();
}
header('Location: convert.php');
?>