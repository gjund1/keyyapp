<?php
require_once(__DIR__ . '/config.php');
requireLogin();

if (!isModerator() && !isAdmin())
    exit("Accès refusé");

$poiId = (int)($_POST['poi_id'] ?? 0);
$userId = (int)($_POST['user_id'] ?? 0);
$newStatus = $_POST['new_status'] ?? '';
$oldStatus = $_POST['old_status'] ?? '';
$allowed = ['VALIDATED', 'REFUSED', 'PENDING'];

if (!$poiId || !in_array($newStatus, $allowed))
    exit("Erreur");

$stmt = $pdo->prepare("UPDATE pois SET status = ? WHERE id = ?");
$stmt->execute([$newStatus, $poiId]);

if ($newStatus === 'VALIDATED' && $oldStatus !== 'VALIDATED') {
    $stmt = $pdo->prepare("UPDATE users SET points = points + 1 WHERE id = ?");
    $stmt->execute([$userId]);
}

if (($newStatus === 'PENDING' || $newStatus === 'REFUSED') && $oldStatus === 'VALIDATED') {
    $stmt = $pdo->prepare("UPDATE users SET points = GREATEST(points - 1, 0) WHERE id = ?");
    $stmt->execute([$userId]);
}

$_SESSION['success'] = "Statut mis à jour !";
if ($oldStatus == 'PENDING')
    header("Location: liste.php?&pending=1&tri=date&city=");
else
    header("Location: liste.php");
exit;