<?php 
require_once(__DIR__ . '/config.php');
requireLogin();
$poi = $_SESSION['new_poi'];
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
                <!-- <div class="Main-card-content2-adresse">
                </div> -->
                <div class="Main-card-content2-coment">
                    <textarea name="content" placeholder="Ajouter un commentaire (optionnel) ?" maxlength="500" form="saveForm"></textarea>
                </div>
                <div class="Main-card-content2-btn">
                    <form id="saveForm" action="add_save.php" method="POST">
                        <button type="submit" class="Btn">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="lightbox" class="Lightbox">
        <span id="lightboxClose" class="Lightbox-close">&times;</span>
        <img id="lightboxImg" class="Lightbox-img" src="" alt="">
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>