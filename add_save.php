<?php require_once(__DIR__ . '/config.php');
requireLogin();

if (!isset($_SESSION['new_poi'])) {
    header('Location: add.php');
    exit;
}

$temp = $_SESSION['new_poi'];
$visibility = $_POST['visibility'] ?? 'PUBLIC';
$content = trim($_POST['content'] ?? NULL);

// table POIS
$stmt = $pdo->prepare("INSERT INTO pois (latitude, longitude, content, user_id, visibility, status, address, city, quartier, cp, region, pays) VALUES (?, ?, ?, ?, ?, 'PENDING', ?, ?, ?, ?, ?, ?)");
$stmt->execute([$temp['lat'], $temp['lon'], $content, $_SESSION['id'], $visibility, $temp['address'], $temp['city'], $temp['quartier'], $temp['cp'], $temp['region'], $temp['pays']]);
$poiId = $pdo->lastInsertId();

// IMAGE TMP -> FINAL
$tmpFile = __DIR__ . '/' . $temp['photo'];
$finalName = 'poi' . $poiId . '_' . uniqid() . '.webp';
$finalPath = __DIR__ . '/img/' . $finalName;
$finalRelativePath = 'img/' . $finalName;
if (file_exists($tmpFile))
    rename($tmpFile, $finalPath);

// table PHOTO
$stmt = $pdo->prepare("INSERT INTO photos (poi_id, file_path) VALUES (?, ?)");
$stmt->execute([$poiId, $finalRelativePath]);

unset($_SESSION['new_poi']);
// header("Location: card.php?id=" . $poiId);
$_SESSION['success'] = "enregistré avec succès !";
header("Location: liste.php?mesboites=1&tri=date&city=");
exit;