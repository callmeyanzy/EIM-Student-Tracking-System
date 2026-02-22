<?php
require_once __DIR__ . '/bootstrap.php';

if (empty($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$user = $_SESSION['user'];

// Load models for dashboard stats
require_once __DIR__ . '/models/Student.php';
require_once __DIR__ . '/models/StudentCompetency.php';
require_once __DIR__ . '/models/Assessment.php';

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/dashboard.php';
require __DIR__ . '/views/layouts/footer.php';
