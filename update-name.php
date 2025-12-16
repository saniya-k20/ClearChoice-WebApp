<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => ''];

try {
    // Verify request method
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method', 405);
    }

    // Get and validate input
    $data = json_decode(file_get_contents('php://input'), true);
    $newName = trim($data['newName'] ?? '');

    if (empty($newName)) {
        throw new Exception('Name cannot be empty', 400);
    }

    if (strlen($newName) > 100) {
        throw new Exception('Name is too long (max 100 characters)', 400);
    }

    // Verify user is logged in
    if (!isset($_SESSION['user']['id'])) {
        throw new Exception('User not authenticated', 401);
    }

    // Update database
    $stmt = $pdo->prepare("UPDATE users SET full_name = ? WHERE id = ?");
    $success = $stmt->execute([$newName, $_SESSION['user']['id']]);

    if (!$success) {
        throw new Exception('Failed to update database', 500);
    }

    // Update session
    $_SESSION['user']['fullName'] = $newName;

    $response = [
        'success' => true,
        'message' => 'Name updated successfully',
        'newName' => $newName
    ];

} catch (Exception $e) {
    http_response_code($e->getCode() >= 400 ? $e->getCode() : 500);
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>