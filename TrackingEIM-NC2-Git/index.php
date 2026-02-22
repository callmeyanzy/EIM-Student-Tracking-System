<?php
// index.php - Redirect to login or dashboard

session_start();

if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
