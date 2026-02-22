<?php
require_once __DIR__ . '/bootstrap.php';

// If already logged in, redirect to dashboard
if (!empty($_SESSION['user'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    $token = $_POST['csrf_token'] ?? '';
    $sessionToken = $_SESSION['__csrf_token'] ?? 'NOT SET';
    if (!csrf_check($token)) {
        $error = 'Invalid form token. Session: ' . substr($sessionToken, 0, 10) . '... POST: ' . substr($token, 0, 10) . '...';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            $error = 'Please provide email and password.';
        } else {
            $pdo = Database::getConnection();
            $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // successful login
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email'],
                    'role' => $user['role']
                ];
                // regenerate session id
                session_regenerate_id(true);
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid credentials.';
            }
        }
    }
}

require __DIR__ . '/views/layouts/header.php';
require __DIR__ . '/views/login.php';
require __DIR__ . '/views/layouts/footer.php';
