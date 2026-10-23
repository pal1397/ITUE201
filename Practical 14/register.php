<?php
require __DIR__ . '/config/mysqli.php';
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if (!preg_match('/^[A-Za-z ]{2,100}$/', $name) || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        $message = 'Enter a valid name/email and a password of at least 8 characters.';
    } else {
        $check = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
        $check->bind_param('s', $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $message = 'This email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert = $mysqli->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')");
            $insert->bind_param('sss', $name, $email, $hash);
            $message = $insert->execute() ? 'Registration successful. You can now log in.' : 'Registration failed. Please try again.';
            $insert->close();
        }
        $check->close();
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Secure Registration</title><link rel="stylesheet" href="css/php-style.css"></head><body><main class="box"><h1>StudentHub Registration</h1><p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p><form method="post"><label>Name</label><input name="name" required><label>Email</label><input name="email" type="email" required><label>Password</label><input name="password" type="password" minlength="8" required><button>Register</button></form><p><a href="login.php">Login</a></p></main></body></html>
