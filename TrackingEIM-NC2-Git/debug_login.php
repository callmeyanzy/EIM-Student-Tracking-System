<?php
require_once __DIR__ . '/bootstrap.php';

echo "=== Login Debug ===\n\n";

echo "Session data:\n";
print_r($_SESSION);

echo "\nPOST data:\n";
print_r($_POST);

echo "\nCSRF Token in session: " . ($_SESSION['__csrf_token'] ?? 'NOT SET') . "\n";
echo "CSRF Token in POST: " . ($_POST['csrf_token'] ?? 'NOT SET') . "\n";

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    echo "\nTrying to login with: {$email}\n";
    
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    
    if ($user) {
        echo "User found!\n";
        $verify = password_verify($password, $user['password']);
        echo "Password verify: " . ($verify ? "SUCCESS" : "FAILED") . "\n";
    } else {
        echo "User NOT found!\n";
    }
}
?>
<form method="post">
    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
    Email: <input type="email" name="email" value="admin@example.com"><br>
    Password: <input type="password" name="password" value="admin123"><br>
    <button type="submit">Test Login</button>
</form>
