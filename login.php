<?php
require_once 'config.php';
clean_output();

session_start();
header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Get and validate input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON input', 400);
    }

    // Verify CSRF token
    if (empty($data['csrf_token']) || $data['csrf_token'] !== $_SESSION['csrf_token']) {
        throw new Exception('CSRF token validation failed', 403);
    }

    $email = filter_var($data['email'] ?? '', FILTER_SANITIZE_EMAIL);
    $password = $data['password'] ?? '';

    // Validate inputs
    if (empty($email)) throw new Exception('Email is required', 400);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new Exception('Invalid email format', 400);
    if (empty($password)) throw new Exception('Password is required', 400);

    // Database operation
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        throw new Exception('Invalid credentials', 401);
    }

    // Regenerate session ID for security
    session_regenerate_id(true);
    
    // Store user data in session
    $_SESSION['user'] = [
        'id' => $user['id'],
        'email' => $user['email'],
        'fullName' => $user['full_name'],
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
    ];

    // Generate new CSRF token after successful login
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    $response = [
        'success' => true,
        'show_popup' => true,
        'popup_message' => 'Login successful',
        'redirect' => 'index.php',
        'user' => ['email' => $user['email'], 'name' => $user['full_name']]
    ];

} catch (Exception $e) {
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>