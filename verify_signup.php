<?php
require 'config.php';

header('Content-Type: text/plain');
echo "=== Signup Verification ===\n\n";

// 1. Check database connection
try {
    $pdo->query("SELECT 1");
    echo "[✓] Database connection working\n";
} catch (PDOException $e) {
    die("[✗] Database connection failed: ".$e->getMessage());
}

// 2. Check table exists
$tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
echo $tableExists ? "[✓] Users table exists\n" : "[✗] Users table missing\n";

// 3. Check last 5 signups
echo "\nLast 5 signups:\n";
$recentUsers = $pdo->query("SELECT id, email, created_at FROM users ORDER BY id DESC LIMIT 5")->fetchAll();
if (empty($recentUsers)) {
    echo "No users found in database\n";
} else {
    foreach ($recentUsers as $user) {
        printf("#%d: %s (%s)\n", $user['id'], $user['email'], $user['created_at']);
    }
}

// 4. Check permissions
echo "\nDatabase permissions:\n";
$perms = $pdo->query("SHOW GRANTS")->fetchAll();
foreach ($perms as $perm) {
    echo $perm[0]."\n";
}