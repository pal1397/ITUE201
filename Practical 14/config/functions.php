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

function issue_remember_token(PDO $pdo, int $userId): void {
    $selector = bin2hex(random_bytes(12));
    $validator = bin2hex(random_bytes(32));
    $expires = time() + (30 * 24 * 60 * 60);
    $hash = hash('sha256', $validator);
    $stmt = $pdo->prepare('INSERT INTO remember_tokens(user_id, selector, token_hash, expires_at) VALUES(?,?,?,FROM_UNIXTIME(?))');
    $stmt->execute([$userId, $selector, $hash, $expires]);
    setcookie('studenthub_remember', $selector . ':' . $validator, [
        'expires' => $expires, 'path' => '/', 'secure' => !empty($_SERVER['HTTPS']),
        'httponly' => true, 'samesite' => 'Lax'
    ]);
}
function clear_remember_token(PDO $pdo): void {
    $cookie = $_COOKIE['studenthub_remember'] ?? '';
    if (strpos($cookie, ':') !== false) {
        [$selector] = explode(':', $cookie, 2);
        $stmt = $pdo->prepare('DELETE FROM remember_tokens WHERE selector=?');
        $stmt->execute([$selector]);
    }
    setcookie('studenthub_remember', '', ['expires'=>time()-3600, 'path'=>'/', 'httponly'=>true, 'samesite'=>'Lax']);
}
function restore_remembered_login(PDO $pdo): void {
    if (!empty($_SESSION['user_id']) || empty($_COOKIE['studenthub_remember'])) return;
    $parts = explode(':', $_COOKIE['studenthub_remember'], 2);
    if (count($parts) !== 2) { clear_remember_token($pdo); return; }
    [$selector, $validator] = $parts;
    $stmt = $pdo->prepare('SELECT rt.*, u.name, u.role FROM remember_tokens rt JOIN users u ON u.id=rt.user_id WHERE rt.selector=? AND rt.expires_at>NOW()');
    $stmt->execute([$selector]); $row = $stmt->fetch();
    if ($row && hash_equals($row['token_hash'], hash('sha256', $validator))) {
        session_regenerate_id(true); $_SESSION['user_id']=(int)$row['user_id']; $_SESSION['name']=$row['name'];
        $_SESSION['role']=$row['role']; $_SESSION['last_activity']=time();
        $pdo->prepare('DELETE FROM remember_tokens WHERE selector=?')->execute([$selector]);
        issue_remember_token($pdo, (int)$row['user_id']);
    } else { clear_remember_token($pdo); }
}

function role_menu(string $role): array {
    if ($role === 'admin') return [['label'=>'Student CRUD','url'=>'students.php'],['label'=>'Event CRUD','url'=>'events_admin.php'],['label'=>'AJAX CRUD','url'=>'practical13.html'],['label'=>'Audit Logs','url'=>'#auditLogs']];
    return [['label'=>'My Dashboard','url'=>'student_dashboard.php'],['label'=>'Events','url'=>'events.php'],['label'=>'My Profile','url'=>'profile.php']];
}
