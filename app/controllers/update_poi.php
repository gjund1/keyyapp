<?php
require_once(__DIR__ . '/config.php');
requireLogin();

$poiId = (int)($_POST['poi_id'] ?? 0);
$field = trim($_POST['field'] ?? null);
$value = trim($_POST['value'] ?? '');

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
$isOwner = false;
$stmt = $pdo->prepare("SELECT user_id FROM pois WHERE id = ?");
$stmt->execute([$poiId]);
$poi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$poi) {
    echo json_encode(['success' => false, 'error' => 'not found']);
    exit;
}

if (isset($_SESSION['id']) && $_SESSION['id'] == $poi['user_id'])
    $isOwner = true;

if (!$isOwner && !isAdmin() && !isModerator()) {
    echo json_encode(['success' => false, 'error' => 'not allowed']);
    exit;
}

/* update dynamique */
$sql = "UPDATE pois SET $field = ? WHERE id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$value, $poiId]);

/* si commentaire modifié -> repasse en attente */
if ($field === 'content') {
    // si était VALIDATED → retirer 1 point
    $stmt = $pdo->prepare("SELECT status, user_id FROM pois WHERE id = ?");
    $stmt->execute([$poiId]);
    $poi = $stmt->fetch(PDO::FETCH_ASSOC);

    // if (!$poi) {
    //     echo json_encode(['success' => false, 'error' => 'not found']);
    //     exit;
    // }


    if ($poi['status'] === 'VALIDATED') {
        $stmt = $pdo->prepare("UPDATE users SET points = GREATEST(points - 1, 0) WHERE id = ?");
        $stmt->execute([$poi['user_id']]);
    }

    $stmt = $pdo->prepare(" UPDATE pois SET status = 'PENDING' WHERE id = ?");
    $stmt->execute([$poiId]);
}

echo json_encode(['success' => true]);