<?php
// config.php - ULTRA-STRICT VERSION
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Prevent any accidental output
if (ob_get_level() === 0) ob_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'clearchoice');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session handling with strict cookie params
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400,
        'path' => '/',
        'domain' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'secure' => isset($_SERVER['HTTPS']),
        'httponly' => true,
        'samesite' => 'Strict'
    ]);
    session_start();
}

// Database connection with error suppression
try {
    $pdo = new PDO(
        "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false
        ]
    );
} catch (PDOException $e) {
    error_log('DB Connection Error: '.$e->getMessage());
    if (!headers_sent()) {
        header('HTTP/1.1 503 Service Unavailable');
    }
    die('Database connection failed');
}

// Add this right after PDO connection:
try {
    $test = $pdo->query("SELECT 1");
    file_put_contents('db_test.log', "Database connection successful at " . date('Y-m-d H:i:s'), FILE_APPEND);
} catch (PDOException $e) {
    file_put_contents('db_errors.log', "Database connection failed: " . $e->getMessage(), FILE_APPEND);
    die(json_encode(['error' => 'Database connection failed']));
}
// Global cleanup function
function clean_output() {
    while (ob_get_level() > 0) ob_end_clean();
}
?>