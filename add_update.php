<?php

require_once(__DIR__ . '/config.php');
requireLogin();

// =====================================
// 1. RÉCUPÉRATION DES DONNÉES
// =====================================

$oldPoiId = (int)($_POST['old_poi_id'] ?? 0);

if (!$oldPoiId || !isset($_SESSION['new_poi'])) {
    $_SESSION['error'] = "Données de remplacement manquantes.";
    header('Location: add.php');
    exit;
}

$temp = $_SESSION['new_poi'];
$userId = (int)$_SESSION['id'];
$content = trim($_POST['content'] ?? '');
$visibility = $_POST['visibility'] ?? 'PUBLIC';

// Vérification de la visibilité
if (!in_array($visibility, ['PUBLIC', 'PRIVATE'], true))
    $visibility = 'PUBLIC';

// Vérification GPS
$lat = filter_var($temp['lat'], FILTER_VALIDATE_FLOAT);
$lon = filter_var($temp['lon'], FILTER_VALIDATE_FLOAT);

if ($lat === false || $lon === false || $lat < -90 || $lat > 90 || $lon < -180 || $lon > 180) {
    $_SESSION['error'] = "Coordonnées GPS invalides.";
    header('Location: add.php');
    exit;
}

// Vérification du fichier temporaire
$photoRelativePath = $temp['photo'] ?? '';

if (!preg_match('~^img/tmp/[a-zA-Z0-9._-]+$~', $photoRelativePath)) {
    $_SESSION['error'] = "Photo temporaire invalide.";
    header('Location: add.php');
    exit;
}

$tmpFile = __DIR__ . '/' . $photoRelativePath;

if (!is_file($tmpFile)) {
    $_SESSION['error'] = "La photo temporaire est introuvable.";
    header('Location: add.php');
    exit;
}

// =====================================
// 2. TRANSACTION SQL
// =====================================

$finalPath = null;

try {

    $pdo->beginTransaction();
    // Récupérer et verrouiller l'ancien POI
    $stmt = $pdo->prepare("SELECT id, user_id, status FROM pois WHERE id = ? FOR UPDATE");
    $stmt->execute([$oldPoiId]);
    $oldPoi = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$oldPoi)
        throw new Exception("POI introuvable.");

    // Vérifier que le POI peut être remplacé
    if (!in_array($oldPoi['status'], ['PENDING', 'VALIDATED'], true))
        throw new Exception("Ce POI ne peut plus être remplacé.");

    // Vérifier les droits
    $isOwner = ((int)$oldPoi['user_id'] === $userId);

    if (!$isOwner && !isAdmin() && !isModerator())
        throw new Exception("Vous n'avez pas le droit de remplacer ce POI.");

    // =====================================
    // 3. ANNULER L'ANCIEN POI
    // =====================================

    $stmt = $pdo->prepare("UPDATE pois SET status = 'CANCELED' WHERE id = ?");
    $stmt->execute([$oldPoiId]);

    // =====================================
    // 4. CRÉER LE NOUVEAU POI
    // =====================================

    $stmt = $pdo->prepare("INSERT INTO pois ( latitude, longitude, content, user_id, visibility, status, address, city, quartier, cp, region, pays)
        VALUES (?, ?, ?, ?, ?, 'PENDING', ?, ?, ?, ?, ?, ?)");

    $stmt->execute([$lat, $lon, $content, $userId, $visibility,
        $temp['address'] ?? null, $temp['city'] ?? null, $temp['quartier'] ?? null, $temp['cp'] ?? null, $temp['region'] ?? null, $temp['pays'] ?? null]);
    $newPoiId = (int)$pdo->lastInsertId();

    // =====================================
    // 5. DÉPLACER LA NOUVELLE PHOTO
    // =====================================

    $finalName = 'poi' . $newPoiId . '_' . uniqid() . '.webp';
    $finalPath = __DIR__ . '/img/' . $finalName;
    $finalRelativePath = 'img/' . $finalName;
    if (!rename($tmpFile, $finalPath))
        throw new Exception("Impossible de déplacer la nouvelle photo.");

    // =====================================
    // 6. ENREGISTRER LA PHOTO
    // =====================================

    $stmt = $pdo->prepare("INSERT INTO photos (poi_id, file_path) VALUES (?, ?)");
    $stmt->execute([ $newPoiId, $finalRelativePath]);

    // =====================================
    // 7. VALIDATION
    // =====================================

    $pdo->commit();

    unset($_SESSION['new_poi']);
    $_SESSION['success'] = "La boîte a été remplacée avec succès ! Le nouveau POI est en attente de modération.";
    header('Location: liste.php?mesboites=1&tri=date&city=');
    exit;

} catch (Throwable $e) {

    // Annuler les modifications SQL
    if ($pdo->inTransaction())
        $pdo->rollBack();

    // Supprimer uniquement la nouvelle photo
    if ($finalPath && file_exists($finalPath))
        unlink($finalPath);

    error_log("Erreur remplacement POI : " . $e->getMessage());
    $_SESSION['error'] = $e->getMessage();
    header('Location: add_confirm.php');
    exit;
}