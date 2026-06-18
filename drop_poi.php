<?php
require_once(__DIR__ . '/config.php');
requireLogin();

$poiId = (int)($_POST['poi_id'] ?? 0);

/* récupérer les photos */
$stmt = $pdo->prepare("SELECT file_path FROM photos WHERE poi_id = ?");
$stmt->execute([$poiId]);
$photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* supprimer les fichiers */
foreach ($photos as $photo) {
    $file = __DIR__ . '/' . $photo['file_path'];
    if (file_exists($file))
        unlink($file);
}

/* supprimer les photos en base */
$stmt = $pdo->prepare("DELETE FROM photos WHERE poi_id = ?");
$stmt->execute([$poiId]);

/* supprimer le POI */
$stmt = $pdo->prepare("DELETE FROM pois WHERE id = ? AND user_id = ?");
$stmt->execute([$poiId, $_SESSION['id']]);

$_SESSION['success'] = "Boîte supprimée de la DB !";
header("Location: liste.php?mesboites=1&tri=date&city=");
exit;