<?php
require_once(__DIR__ . '/config.php');
requireLogin();

$username = trim($_POST['username'] ?? '');

if (!$username) {
    echo json_encode(['success' => false]);
    exit;
}

/* longueur mini */
if (mb_strlen($username) < 1) {
    echo json_encode(['success' => false]);
    exit;
}

$stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
$stmt->execute([$username, $_SESSION['id']]);
$_SESSION['name'] = $username;

echo json_encode([
    'success' => true,
    'username' => htmlspecialchars($username)
]);