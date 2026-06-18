<?php
require_once(__DIR__ . '/config.php');
requireLogin();

$poiId = (int)($_POST['poi_id'] ?? 0);
$field = $_POST['field'] ?? null;
$value = $_POST['value'] ?? null;

if (!$poiId || !$field) {
    echo json_encode(['success' => false]);
    exit;
}

/* sécurité : whitelist des champs modifiables */
$allowed = ['visibility', 'content'];

if (!in_array($field, $allowed)) {
    echo json_encode(['success' => false, 'error' => 'invalid field']);
    exit;
}

/* check ownership */
$stmt = $pdo->prepare("SELECT id FROM pois WHERE id = ? AND user_id = ?");
$stmt->execute([$poiId, $_SESSION['id']]);

if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'not allowed']);
    exit;
}

/* update dynamique */
$sql = "UPDATE pois SET $field = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$value, $poiId]);

echo json_encode(['success' => true]);