<?php require_once(__DIR__ . '/config.php'); ?>
<?php require_once(__DIR__ . '/header.php'); ?>
<script>const currentUserId = <?= $_SESSION['id'] ?? 'null' ?>;</script>

<?php 
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: liste.php');
    exit;
}
$fiche_id = (int)$_GET['id'];

$stmt = $pdo->prepare("SELECT pois.*, photos.file_path FROM pois LEFT JOIN photos ON photos.poi_id = pois.id WHERE pois.id = ? AND pois.status != 'CANCELED' LIMIT 1");
$stmt->execute([$fiche_id]);
$poi = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$poi) {
    header('Location: liste.php');
    exit;
}

$isOwner = isset($_SESSION['id']) && $_SESSION['id'] == $poi['user_id'];
$isPublicValid = $poi['status'] === 'VALIDATED' && $poi['visibility'] === 'PUBLIC';

if (!$isOwner && !$isPublicValid && !isModerator() && !isAdmin()) {
    header('Location: liste.php');
    exit;
}

$statusText = match($poi['status']) {
    'VALIDATED' => 'validé',
    'PENDING' => 'en attente',
    'REFUSED' => 'refusé',
    'CANCELED' => 'supprimé',
    default => $poi['status']
};

function statusClass($status) {
    return match ($status) {
        "VALIDATED" => "status-green",
        "PENDING" => "status-orange",
        "REFUSED" => "status-red",
        default => "status-brown"
    };
}

function formatDateFr($dateString) {
    if (empty($dateString))
        return '';

    $date = new DateTime($dateString);
    $formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::SHORT);
    $formatter->setPattern("d MMMM yyyy 'à' HH:mm");
    return $formatter->format($date);
}

?>

<script>
    const cardLat = <?= (float)$poi['latitude'] ?>;
    const cardLon = <?= (float)$poi['longitude'] ?>;
    const poiId = <?= (int)$poi['id'] ?>;
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="../../public/assets/css/index.css">
<link rel="stylesheet" href="../../public/assets/css/card.css">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="../../public/assets/js/index.js" defer></script>
<script src="../../public/assets/js/map_fiche.js" defer></script>
<script src="../../public/assets/js/card.js" defer></script>

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
            <img id="poiImage" class="Main-card-photo" src="<?= htmlspecialchars($poi['file_path'], ENT_QUOTES, 'UTF-8') ?>" alt="boites">
            <div class="Main-card-content1">
                <p class="Main-card-content1-lat">Latitude : <?= htmlspecialchars($poi['latitude']) ?></p>
                <p class="Main-card-content1-lon">Longitude : <?= htmlspecialchars($poi['longitude']) ?></p>
                <p class="Main-card-content1-distance">Distance inconnue</p>
                <a class="Main-card-content1-link" href="https://www.google.com/maps/dir/?api=1&destination=<?= htmlspecialchars(urlencode($poi['latitude'] . ',' . $poi['longitude'])) ?>" target="_blank"><i>-> Itineraire</i>  </a>
                <?php if ($isOwner || isModerator() || isAdmin()) : ?>
                    <br><br>                                     
                    <p>
                        <label class="Main-card-content1-vidibility" for="vidibility">Affichage : </label>
                        <select name="visibility" id="visibility">
                            <option value="PUBLIC" <?= ($poi['visibility'] ?? '') === 'PUBLIC' ? 'selected' : '' ?>>Public</option>
                            <option value="PRIVATE" <?= ($poi['visibility'] ?? '') === 'PRIVATE' ? 'selected' : '' ?>>Privé</option>
                        </select>
                    </p>
                    <p class="Main-card-content1-status">Statut : <span class="Main-card-content1-status-span <?= statusClass(htmlspecialchars($poi['status'])) ?>"><?= htmlspecialchars($statusText) ?></span></p>
                <?php endif; ?>
                <p class="Main-card-content1-ajoute">Ajouté le <?= htmlspecialchars(formatDateFr($poi['created_at'])) ?></p><br>
            </div>
            <div class="Main-card-content2">
                <div class="Main-card-content2-adresse">
                    <p class="Main-card-content2-city"><span class="Main-card-content2-city-span"><?= htmlspecialchars($poi['city']) ?></span> (<?= htmlspecialchars($poi['cp']) ?>)</p>
                    <p class="Main-card-content2-dep"><?= htmlspecialchars($poi['region']) ?> (<?= htmlspecialchars($poi['pays']) ?>)</p>
                    <p class="Main-card-content2-street"><?= htmlspecialchars($poi['address']) ?></p>
                    <p class="Main-card-content2-quartier">Quartier : <?= htmlspecialchars($poi['quartier']) ?></p>
                </div>

                <?php if ($poi['content'] || $isOwner || isModerator() || isAdmin()) : ?>
                    <div class="Main-card-content2-coment">
                        <textarea name="content" placeholder="<?= empty(htmlspecialchars($poi['content'] ?? '')) ? 'Ajouter un commentaire (optionnel) ?' : '' ?>" maxlength="500"><?= htmlspecialchars($poi['content'] ?? '') ?></textarea>
                    </div>
                <?php endif; ?>

                <p class="message"></p>

                <?php if ($isOwner || isModerator() || isAdmin()) : ?>
                    <div class="Main-card-content2-btn">
                        <?php if (isModerator() || isAdmin()) : ?>
                            <form action="moderate_poi.php" method="POST">
                                <input type="hidden" name="poi_id" value="<?= (int)$poi['id'] ?>">
                                <input type="hidden" name="user_id" value="<?= (int)$poi['user_id'] ?>">
                                <input type="hidden" name="old_status" value="<?= htmlspecialchars($poi['status']) ?>">
                                <?php if ($poi['status'] === "PENDING") : ?>
                                    <button type="submit" name="new_status" value="VALIDATED" class="Main-card-valid Btn">Valider</button>
                                    <button type="submit" name="new_status" value="REFUSED" class="Main-card-refuse Btn">Refuser</button>
                                <?php endif; ?>
                                <?php if ($poi['status'] === "VALIDATED" || $poi['status'] === "REFUSED") : ?>
                                    <button type="submit" name="new_status" value="PENDING" class="Main-card-pending Btn">Pending</button>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>
                        <button class="Main-card-btn-del Btn">Supprimer</button>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <div id="lightbox" class="Lightbox">
        <span id="lightboxClose" class="Lightbox-close">&times;</span>
        <img id="lightboxImg" class="Lightbox-img" src="" alt="">
    </div>
    <div id="deleteModal" class="Modal">
        <div class="Modal-content">
            <h3>Supprimer la boîte ?</h3>
            <p>Cette action est irréversible !</p>
            <div class="Modal-buttons">
                <button id="btnCancelDelete" class="Btn">Annuler</button>
                <form action="delete_poi.php" method="POST">
                    <input type="hidden" name="poi_id" value="<?= htmlspecialchars($poi['id']) ?>">
                    <button type="submit" class="Btn Btn-danger">Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>