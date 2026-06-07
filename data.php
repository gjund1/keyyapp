<?php

$data = [];

if (isLogged()) {
    // USER : POI publics validés + ses POI
    $stmt = $pdo->prepare("
        SELECT pois.*, photos.file_path FROM pois
        LEFT JOIN photos ON photos.poi_id = pois.id
        WHERE (pois.status = 'VALIDATED' AND pois.visibility = 'PUBLIC') OR pois.user_id = ?
    ");
    $stmt->execute([$_SESSION['id']]);
} else {
    // ANONYME : uniquement POI publics validés
    $stmt = $pdo->prepare("
        SELECT pois.*, photos.file_path FROM pois
        LEFT JOIN photos ON photos.poi_id = pois.id
        WHERE pois.status = 'VALIDATED' AND pois.visibility = 'PUBLIC'
    ");
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
        "user_id" => (int)$poi["user_id"]
    ];
}

