<?php
session_start();

try {
    $pdo = new PDO("mysql:host=DB_HOST;dbname=DB_NAME;charset=utf8", DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die($e->getMessage());
}

require_once(__DIR__ . '/auth.php');
        
// Utilisateur visiteur par défaut
$_SESSION["role"] = $_SESSION["role"] ?? "ANONYMOUS";

// Update last_seen_at
if (isLogged()) {
    $stmt = $pdo->prepare("UPDATE users SET last_seen_at = NOW() WHERE id = ?");
    $stmt->execute([$_SESSION["user_id"]]);
}