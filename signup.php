<?php
require_once 'config.php';
clean_output();

// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Set JSON header
header('Content-Type: application/json');

try {
    // 1. Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Invalid request method", 405);
    }

    // 2. Get and validate input data
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Invalid JSON input", 400);
    }

    // 3. Extract and sanitize inputs
    $fullName = isset($input['fullName']) ? trim($input['fullName']) : '';
    $email = isset($input['email']) ? filter_var(trim($input['email']), FILTER_SANITIZE_EMAIL) : '';
    $password = $input['password'] ?? '';
    $confirmPassword = $input['confirmPassword'] ?? '';

    // 4. Validate inputs
    $errors = [];

    if (empty($fullName)) {
        $errors['fullName'] = "Full name is required";
    } elseif (strlen($fullName) < 2) {
        $errors['fullName'] = "Full name must be at least 2 characters";
    }

    if (empty($email)) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }

    if ($password !== $confirmPassword) {
        $errors['confirmPassword'] = "Passwords do not match";
    }

    if (!empty($errors)) {
        throw new Exception(json_encode($errors), 400);
    }

    // 5. Check if email exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $errors['email'] = "Email already exists";
        throw new Exception(json_encode($errors), 409);
    }

    // 6. Insert new user
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
    
    if (!$stmt->execute([$fullName, $email, $hashedPassword])) {
        throw new Exception("Database insert failed", 500);
    }

    // Start session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Set signup success flag and email in session
    $_SESSION['signup_success'] = true;
    $_SESSION['signup_email'] = $email;

    // 7. Return success response
    echo json_encode([
        'success' => true,
        'redirect' => 'index.php?form=login',
        'user' => [
            'id' => $pdo->lastInsertId(),
            'email' => $email
        ]
    ]);

} catch (Exception $e) {
    $response = [
        'success' => false,
        'message' => $e->getMessage()
    ];

    // Handle validation errors differently
    if ($e->getCode() === 400 || $e->getCode() === 409) {
        $response['errors'] = json_decode($e->getMessage(), true);
    }

    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    echo json_encode($response);
}
?>