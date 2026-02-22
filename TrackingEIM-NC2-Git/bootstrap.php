<?php
// bootstrap.php - central loader and security setup
// Include at top of public PHP pages

declare(strict_types=1);

// Start session with secure settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}

// Session timeout (30 minutes)
$timeout = 30 * 60;
if (!empty($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
    session_start();
}
$_SESSION['LAST_ACTIVITY'] = time();

// Basic security headers
header('X-Frame-Options: SAMEORIGIN');
header('X-Content-Type-Options: nosniff');
header('X-XSS-Protection: 1; mode=block');

// Autoloader for models and controllers
spl_autoload_register(function ($class) {
    $paths = [__DIR__ . '/models/', __DIR__ . '/controllers/'];
    foreach ($paths as $p) {
        $file = $p . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return true;
        }
    }
    return false;
});

// Load database connector
require_once __DIR__ . '/config/database.php';

// CSRF helpers
function csrf_token(): string
{
    if (empty($_SESSION['__csrf_token'])) {
        $_SESSION['__csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['__csrf_token'];
}

function csrf_check(string $token): bool
{
    return hash_equals($_SESSION['__csrf_token'] ?? '', $token);
}

// Helper for escaping
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
