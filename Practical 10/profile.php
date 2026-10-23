<?php
require __DIR__ . '/config/functions.php';
require_login();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>My Profile</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box"><h1>My Profile</h1><p><strong>Name:</strong> <?=e($_SESSION['name'] ?? '')?></p><p><strong>Role:</strong> <?=e($_SESSION['role'] ?? 'student')?></p><p><a href="student_dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a></p></main></body></html>
