<?php
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}
function e($value) { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function require_login() {
    if (empty($_SESSION['user_id'])) { header('Location: login.php'); exit; }
    if (!empty($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > 1800) {
        $_SESSION = []; session_destroy(); header('Location: login.php?timeout=1'); exit;
    }
    $_SESSION['last_activity'] = time();
}
function require_admin() {
    require_login();
    if (($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); exit('Access denied. Admin only.'); }
}
function audit_log($pdo, $action, $details='') {
    $stmt = $pdo->prepare('INSERT INTO audit_logs(user_id,action,details) VALUES(?,?,?)');
    $stmt->execute([$_SESSION['user_id'] ?? null, $action, $details]);
}
