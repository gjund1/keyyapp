

<?php
session_start();

$lat = $_POST['lat'] ?? null;
$lon = $_POST['lon'] ?? null;

if (!isset($_FILES['photo']))
    die(json_encode(['error' => 'no file']));

$tmpDir = __DIR__ . '../../public/uploads/pois/tmp/';
if (!is_dir($tmpDir))
    mkdir($tmpDir, 0777, true);

$filename = 'tmp_' . uniqid() . '.webp';
$fullPath = $tmpDir . $filename;
move_uploaded_file($_FILES['photo']['tmp_name'], $fullPath);

// photos abandonnées sont supprimées après 24h.
foreach (glob(__DIR__ . '../../public/uploads/pois/tmp/*') as $file) {
    if (time() - filemtime($file) > 86400)
        unlink($file);
}

/* =========================
   2. REVERSE GEOCODING (OSM)
========================= */

function reverseGeocode($lat, $lon) {
    $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$lat}&lon={$lon}";
    $opts = ["http" => ["method" => "GET", "header" => "User-Agent: KeyMapApp/1.0\r\n"]];

    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);

    if (!$response)
        return null;

    return json_decode($response, true);
}

$geo = reverseGeocode($lat, $lon);
$address = $geo['display_name'] ?? null;
$addressParts = $geo['address'] ?? [];

/* =========================
   3. SESSION
========================= */

$_SESSION['new_poi'] = [
    'photo' => '../../public/uploads/pois/tmp/' . $filename,
    'lat' => $lat,
    'lon' => $lon,
    'address' => $addressParts['road'],
    'quartier' => $addressParts['quarter'],
    'city' => $addressParts['city'],
    'cp' => $addressParts['postcode'],
    'region' => $addressParts['county'],
    'pays' => strtoupper($addressParts['country_code']) 
];

echo json_encode([
    'success' => true,
    'geo' => $geo
]);
exit;


