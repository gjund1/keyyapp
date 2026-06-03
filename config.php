<?php
session_start();

$host = "localhost";
$dbname = "keymap";
$user = "root";
$pass = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
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