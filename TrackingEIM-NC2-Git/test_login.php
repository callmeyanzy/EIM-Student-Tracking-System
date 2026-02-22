<?php
require_once __DIR__ . '/config/database.php';

$email = 'admin@example.com';
$password = 'admin123';

$pdo = Database::getConnection();
$stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
$stmt->execute([':email' => $email]);
$user = $stmt->fetch();

if ($user) {
    echo "User found: {$user['email']}\n";
    echo "Stored hash: {$user['password']}\n";
    echo "Testing password: {$password}\n";
    
    $result = password_verify($password, $user['password']);
    echo "Password verify result: " . ($result ? "TRUE" : "FALSE") . "\n";
    
    if (!$result) {
        echo "\nHash is invalid. Creating new password hash...\n";
        $newHash = password_hash($password, PASSWORD_DEFAULT);
        echo "New hash: {$newHash}\n";
        
        $stmt = $pdo->prepare('UPDATE users SET password = :password WHERE id = :id');
        $stmt->execute([':password' => $newHash, ':id' => $user['id']]);
        echo "Password updated! Try logging in again.\n";
    }
} else {
    echo "User not found!\n";
}
