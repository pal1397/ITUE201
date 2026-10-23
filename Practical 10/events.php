<?php
require __DIR__ . '/config/functions.php';
require __DIR__ . '/config/db.php';
$events = $pdo->query("SELECT title, description, event_date, venue, category, status FROM events ORDER BY event_date")->fetchAll();
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>StudentHub Events</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box wide"><h1>StudentHub Events</h1><p><a href="student_dashboard.php">Dashboard</a> | <a href="logout.php">Logout</a></p><table><tr><th>Event</th><th>Description</th><th>Date</th><th>Venue</th><th>Category</th><th>Status</th></tr><?php foreach($events as $event): ?><tr><td><?=e($event['title'])?></td><td><?=e($event['description'])?></td><td><?=e($event['event_date'])?></td><td><?=e($event['venue'])?></td><td><?=e($event['category'])?></td><td><?=e($event['status'])?></td></tr><?php endforeach; ?></table></main></body></html>
