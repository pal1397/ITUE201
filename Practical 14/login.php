<?php
require __DIR__.'/config/functions.php'; require __DIR__.'/config/db.php';
restore_remembered_login($pdo);
if (!empty($_SESSION['user_id'])) { header('Location: '.(($_SESSION['role']??'')==='admin'?'admin_dashboard.php':'student_dashboard.php')); exit; }
$message = isset($_GET['timeout']) ? 'Session expired. Please log in again.' : '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $email=trim($_POST['email']??''); $password=$_POST['password']??'';
 $stmt=$pdo->prepare('SELECT * FROM users WHERE email=?'); $stmt->execute([$email]); $user=$stmt->fetch();
 if ($user && password_verify($password,$user['password'])) {
  session_regenerate_id(true); $_SESSION['user_id']=$user['id']; $_SESSION['name']=$user['name']; $_SESSION['role']=$user['role']; $_SESSION['last_activity']=time();
  $pdo->prepare('UPDATE users SET last_login=NOW() WHERE id=?')->execute([$user['id']]);
  if (!empty($_POST['remember_me'])) issue_remember_token($pdo, (int)$user['id']);
  audit_log($pdo,'Login','Successful login');
  header('Location: '.($user['role']==='admin'?'admin_dashboard.php':'student_dashboard.php')); exit;
 } else $message='Invalid email or password.';
}
?><!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>StudentHub Login</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box"><h1>StudentHub Login</h1><p><?=e($message)?></p><form method="post"><label>Email</label><input name="email" type="email" required><label>Password</label><input name="password" type="password" required><label><input type="checkbox" name="remember_me" value="1"> Remember me on this device</label><button>Login</button></form><p><a href="register.php">Create account</a></p></main></body></html>
