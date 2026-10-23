<?php
require __DIR__.'/config/functions.php'; require __DIR__.'/config/db.php'; require_admin();
$students=(int)$pdo->query('SELECT COUNT(*) FROM students')->fetchColumn();
$events=(int)$pdo->query('SELECT COUNT(*) FROM events')->fetchColumn();
$users=(int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$registrations=(int)$pdo->query('SELECT COUNT(*) FROM registrations')->fetchColumn();
$latest=$pdo->query('SELECT name,email,created_at FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();
$logs=$pdo->query('SELECT action,details,created_at FROM audit_logs ORDER BY created_at DESC LIMIT 10')->fetchAll();
$role = $_SESSION['role'] ?? 'student';
$menuItems = role_menu($role);
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Admin Dashboard</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box wide">
<h1>StudentHub Admin Dashboard</h1><p>Signed in as <?=e($_SESSION['name'])?> | <a href="logout.php">Logout</a></p>
<div class="stats"><div class="stat"><h3>Users</h3><strong><?=$users?></strong></div><div class="stat"><h3>Students</h3><strong><?=$students?></strong></div><div class="stat"><h3>Events</h3><strong><?=$events?></strong></div><div class="stat"><h3>Registrations</h3><strong><?=$registrations?></strong></div></div>
<h2>Menu (based on user role)</h2><nav><?php foreach($menuItems as $item): ?><a href="<?=e($item['url'])?>"><?=e($item['label'])?></a> | <?php endforeach; ?></nav>
<h2>Latest Registrations / Users</h2><table><tr><th>Name</th><th>Email</th><th>Created</th></tr><?php foreach($latest as $u): ?><tr><td><?=e($u['name'])?></td><td><?=e($u['email'])?></td><td><?=e($u['created_at'])?></td></tr><?php endforeach; ?></table>
<h2 id="auditLogs">Recent Audit Log</h2><table><tr><th>Action</th><th>Details</th><th>Date</th></tr><?php foreach($logs as $log): ?><tr><td><?=e($log['action'])?></td><td><?=e($log['details'])?></td><td><?=e($log['created_at'])?></td></tr><?php endforeach; ?></table>
</main></body></html>
