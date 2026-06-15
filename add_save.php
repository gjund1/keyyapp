<?php require_once(__DIR__ . '/config.php'); ?>

<?php

if (!isset($_SESSION['new_poi'])) {
    exit;
}

$temp = $_SESSION['new_poi'];

$content =
    trim($_POST['content'] ?? '');

$uuid =
    bin2hex(random_bytes(16));

$stmt = $pdo->prepare("
INSERT INTO pois
(
    uuid,
    latitude,
    longitude,
    content,
    user_id,
    visibility,
    status
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?,
    'PUBLIC',
    'PENDING'
)
");

$stmt->execute([
    $uuid,
    $temp['lat'],
    $temp['lon'],
    $content,
    $_SESSION['id']
]);

$poiId =
    $pdo->lastInsertId();