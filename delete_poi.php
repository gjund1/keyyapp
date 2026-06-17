<?php require_once(__DIR__ . '/config.php');
requireLogin();

$poiId = (int)($_POST['poi_id'] ?? 0);
$stmt = $pdo->prepare("UPDATE pois SET status = 'CANCELED' WHERE id = ? AND user_id = ?");
$stmt->execute([$poiId, $_SESSION['id']]);

$_SESSION['success'] = "La boîte a été supprimée.";
header("Location: liste.php?mesboites=1&tri=date&city=");
exit;