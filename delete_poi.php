<?php
require_once(__DIR__ . '/config.php');
requireLogin();

$poiId = (int)($_POST['poi_id'] ?? 0);

if (!$poiId)
    exit("Erreur");

$stmt = $pdo->prepare("SELECT user_id, status FROM pois WHERE id = ?");
$stmt->execute([$poiId]);
$poi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$poi)
    exit("POI introuvable");

$isOwner = ($poi['user_id'] == $_SESSION['id']);
$isModeratorOrAdmin = isModerator() || isAdmin();

if (!$isOwner && !$isModeratorOrAdmin)
    exit("Accès refusé");

$stmt = $pdo->prepare("UPDATE pois SET status = 'CANCELED' WHERE id = ?");
$stmt->execute([$poiId]);

if ($poi['status'] === 'VALIDATED') {
    $stmt = $pdo->prepare("UPDATE users SET points = GREATEST(points - 1, 0) WHERE id = ?");
    $stmt->execute([$poi['user_id']]);
}

$_SESSION['success'] = "La boîte a été supprimée !";
header("Location: liste.php");
exit;