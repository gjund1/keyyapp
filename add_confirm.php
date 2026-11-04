<?php
require_once(__DIR__ . '/config.php');
requireLogin();

// Vérifier que les données de capture existent
if (!isset($_SESSION['new_poi'])) {
    header('Location: add.php');
    exit;
}

$poi = $_SESSION['new_poi'];

// Recherche des POI dans un rayon de 8 mètres
$sql = "SELECT id, user_id, latitude, longitude, address, city, quartier, status, content,
        (6371000 * 2 * ASIN(SQRT(POWER(SIN(RADIANS(latitude - :lat1) / 2), 2) + COS(RADIANS(:lat2)) * COS(RADIANS(latitude)) * POWER(SIN(RADIANS(longitude - :lon) / 2), 2)))) AS distance
        FROM pois WHERE status IN ('PENDING', 'VALIDATED') HAVING distance <= 8 ORDER BY distance ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute(['lat1' => $poi['lat'], 'lat2' => $poi['lat'], 'lon'  => $poi['lon']]);

$nearbyPois = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once(__DIR__ . '/header.php'); ?>

<script>
    const cardLat = <?= (float)$poi['lat'] ?>;
    const cardLon = <?= (float)$poi['lon'] ?>;
</script>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/card.css">
<link rel="stylesheet" href="css/add_save.css">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/index.js" defer></script>
<script src="js/map_fiche.js" defer></script>
<!-- <script src="js/add_confirm.js" defer></script> -->
<script src="js/card.js" defer></script>

<main class="Main">
    <div class="Main-map Main-card">
        <div class="Main-card-top">
            <a href="add.php" class="Main-card-top-back"><span
                    class="material-symbols-outlined font-back">arrow_back</span>retour</a>
            <p class="Main-card-top-title">Capture</p> 
            <p class="Main-card-top-count"></p>
        </div>
        <div class="Main-card-box">

            <div id="map" class="Main-card-map">
            </div>
            <img id="poiImage" class="Main-card-photo" src="<?= htmlspecialchars($poi['photo'], ENT_QUOTES, 'UTF-8') ?>" alt="boites">
            <div class="Main-card-content1">
                <p class="Main-card-content1-lat">Latitude : <?= htmlspecialchars($poi['lat']) ?></p>
                <p class="Main-card-content1-lon">Longitude : <?= htmlspecialchars($poi['lon']) ?></p>
                <br><br>
                <p class="Main-card-content2-city"><span class="Main-card-content2-city-span"><?= htmlspecialchars($poi['city']) ?></span> (<?= htmlspecialchars($poi['cp']) ?>)</p>
                <p class="Main-card-content2-dep"><?= htmlspecialchars($poi['region']) ?> (<?= htmlspecialchars($poi['pays']) ?>)</p>
                <p class="Main-card-content2-street"><?= htmlspecialchars($poi['address']) ?></p>
                <p class="Main-card-content2-quartier">Quartier : <?= htmlspecialchars($poi['quartier']) ?></p>
                <br><br>
                <p>
                    <label class="Main-card-content1-vidibility" for="vidibility">Affichage : </label>
                    <select name="visibility" id="visibility" form="saveForm">
                        <option value="PUBLIC" selected>Public</option>
                        <option value="PRIVATE">Privé</option>
                    </select>
                </p>
            </div>
            
            <div class="Main-card-content2">
                <div class="Main-card-content2-coment">
                    <textarea id="content" name="content" placeholder="Ajouter un commentaire (optionnel) ?" maxlength="500" form="saveForm"></textarea>
                </div>
                <div class="Main-card-content2-btn">
                    <form id="saveForm" action="add_save.php" method="POST">
                        <?php if (empty($nearbyPois)): ?>
                            <button type="submit" class="Btn">Enregistrer</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
                            
            <!-- Avertissement des POI proches -->
            <?php if (!empty($nearbyPois)): ?>
                <div class="Nearby-warning">
                    <h3>⚠️ Des boîtes existent à proximité</h3>
                    <p>Nous avons trouvé <?= count($nearbyPois) ?> boîte<?= count($nearbyPois) > 1 ? 's' : '' ?> dans un rayon de 8 mètres :</p>

                    <?php foreach ($nearbyPois as $nearby): ?>
                        <?php
                            $isOwner = ((int)$nearby['user_id'] === (int)$_SESSION['id']);
                            $canReplace = $isOwner || isAdmin() || isModerator();
                        ?>
                        <div class="Nearby-item">
                            <p><a href="card.php?id=<?= (int)$nearby['id'] ?>" target="_blank" rel="noopener"><strong>Voir la boîte <?= (int)$nearby['id'] ?></strong></a></p>
                            <?php if ($canReplace): ?>
                                <form action="add_update.php" method="POST">
                                    <input type="hidden" name="old_poi_id" value="<?= (int)$nearby['id'] ?>">
                                    <input type="hidden" name="content" value="">
                                    <input type="hidden" name="visibility" value="PUBLIC">
                                    <button type="submit" class="Btn" onclick="
                                        this.form.elements['content'].value = document.getElementById('content').value;
                                        this.form.elements['visibility'].value = document.getElementById('visibility').value;">Remplacer cette boîte
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <button type="button" class="Btn" onclick="document.getElementById('saveForm').requestSubmit()">Ajouter quand même une nouvelle boîte</button>
                    <br><br>
                </div>
            <?php endif; ?>
            
        </div>
    </div>
    <div id="lightbox" class="Lightbox">
        <span id="lightboxClose" class="Lightbox-close">&times;</span>
        <img id="lightboxImg" class="Lightbox-img" src="" alt="">
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>