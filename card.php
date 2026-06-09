<?php require_once(__DIR__ . '/config.php'); ?>
<script>const currentUserId = <?= $_SESSION['id'] ?? 'null' ?>;</script>
<?php require_once(__DIR__ . '/header.php'); ?>

<?php 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit;
}
$fiche_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE pois.id = ? LIMIT 1");
$stmt->execute([$fiche_id]);
$poi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$poi) {
    header('Location: liste.php');
    exit;
}

$isOwner = isset($_SESSION['id']) && $_SESSION['id'] == $poi['user_id'];
$isPublicValid =
    $poi['status'] === 'VALIDATED' &&
    $poi['visibility'] === 'PUBLIC';

if (!$isOwner && !$isPublicValid) {
    http_response_code(403);
    die("Accès interdit");
}

$statusText = match($poi['status']) {
    'VALIDATED' => 'Validé',
    'PENDING' => 'En attente',
    'REFUSED' => 'Refusé',
    'CANCELED' => 'Supprimé',
    default => $poi['status']
};

?>

<script>
    const cardLat = <?= (float)$poi['latitude'] ?>;
    const cardLon = <?= (float)$poi['longitude'] ?>;
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/card.css">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/index.js" defer></script>
<script src="js/map_fiche.js" defer></script>
<script src="js/card.js" defer></script>

<main class="Main">
    <div class="Main-map Main-card">
        <div class="Main-card-top">
            <a href="liste.php" class="Main-card-top-back"><span
                    class="material-symbols-outlined font-back">arrow_back</span>Liste</a>
            <p class="Main-card-top-title">Card <?= htmlspecialchars($poi['id']) ?></p> 
            <p class="Main-card-top-count"></p>
        </div>
        <div class="Main-card-box">
            <div id="map" class="Main-card-map">
                
            </div>
            <img class="Main-card-photo" src="<?= htmlspecialchars($poi['file_path']) ?>" alt="boites">
            <div class="Main-card-content1">
                <p class="Main-card-content1-lat">Latitude : <?= htmlspecialchars($poi['latitude']) ?></p>
                <p class="Main-card-content1-lon">Longitude : <?= htmlspecialchars($poi['longitude']) ?></p>
                <a class="Main-card-content1-link" href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($poi['latitude'] . ',' . $poi['longitude']) ?>" target="_blank"><i>-> Itineraire</i>  </a>
                <br><br>
                <p class="Main-card-content1-distance">Distance inconnue</p>
                <p class="Main-card-content1-ajoute">Ajouté le <?= (new DateTime($poi['created_at']))->format('d F Y \à H:i') ?></p><br>
                <p class="Main-card-content1-vidibility">Affichage : <i><?= ($poi['visibility'] ?? '') === 'PUBLIC' ? 'Public' : 'Privé' ?></i></p>
                <p class="Main-card-content1-status">Statut : <span class="Main-card-content1-status-span"><?= $statusText ?></span></p>
            </div>
            <div class="Main-card-content2">
                <div class="Main-card-content2-adresse">
                    <p class="Main-card-content2-city"><span class="Main-card-content2-city-span"><?= htmlspecialchars($poi['city']) ?></span> (<?= htmlspecialchars($poi['cp']) ?>)</p>
                    <p class="Main-card-content2-dep"><?= htmlspecialchars($poi['region']) ?> (<?= htmlspecialchars($poi['pays']) ?>)</p>
                    <p class="Main-card-content2-street"><?= htmlspecialchars($poi['address']) ?></p>
                    <p class="Main-card-content2-quartier">Quartier : <?= htmlspecialchars($poi['quartier']) ?></p>
                </div>
                <div class="Main-card-content2-coment">
                    <?php if ($poi['content']) : ?>
                    <p>comment : <?= htmlspecialchars($poi['content']) ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($isOwner || isModerator() || isAdmin()) : ?>
                <div class="Main-card-content2-btn">
                    <button class="Main-card-modify Btn">Modifier</button>
                    <button class="Main-card-btn-del Btn">Supprimer</button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>