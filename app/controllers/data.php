<?php

// if (isLogged()) {
//     // USER : POI publics validés + ses POI
//     $stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE (pois.status = 'VALIDATED' AND pois.visibility = 'PUBLIC' AND pois.status != 'CANCELED') OR pois.user_id = ? AND pois.status != 'CANCELED'");
//     $stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE ((pois.status = 'VALIDATED' AND pois.visibility = 'PUBLIC') OR pois.user_id = ?) AND pois.status != 'CANCELED'");
//     $stmt->execute([$_SESSION['id']]);
// } else {
//     // ANONYME : uniquement POI publics validés
//     $stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE pois.status = 'VALIDATED' AND pois.visibility = 'PUBLIC'");
//     $stmt->execute();
// }
// $pois = $stmt->fetchAll(PDO::FETCH_ASSOC);

$data = [];

$isAdmin = function_exists('isAdmin') && isAdmin();
$isModerator = function_exists('isModerator') && isModerator();

if (isLogged()) {
    if (isModerator() || isAdmin()) {
        // ADMIN / MODERATOR : tout sauf CANCELED
        $stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE pois.status != 'CANCELED'");
        $stmt->execute();

    } else {
        // USER : POI publics validés + ses POI
            $stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE (pois.status = 'VALIDATED' AND pois.visibility = 'PUBLIC' AND pois.status != 'CANCELED') OR pois.user_id = ? AND pois.status != 'CANCELED'");
        $stmt->execute([$_SESSION['id']]);
    }
} else {
    // ANONYME : uniquement POI publics validés
    $stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE pois.status = 'VALIDATED' AND pois.visibility = 'PUBLIC'");
    $stmt->execute();
}

$pois = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($pois as $poi) {
    $data[] = [
        "id" => (int)$poi["id"],
        "latitude" => (float)$poi["latitude"],
        "longitude" => (float)$poi["longitude"],
        "address" => $poi["address"],
        "city" => $poi["city"],
        "quartier" => $poi["quartier"],
        "cp" => $poi["cp"],
        "region" => $poi["region"],
        "pays" => $poi["pays"],
        "image" => $poi["file_path"],
        "visibility" => $poi["visibility"],
        "status" => $poi["status"],
        "created_at" => date('c', strtotime($poi["created_at"])),
        "content" => $poi["content"],
        "points" => (int)$poi["points"],
        "user_id" => (int)$poi["user_id"]
    ];
}

