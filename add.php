<?php require_once(__DIR__ . '/config.php'); ?>
<?php require_once(__DIR__ . '/header.php'); ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/add.css">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/add.js" defer></script>

<main class="Main Main-map">
    <div class="Add">            
        <div class="Add-camera">
            <video id="video" class = "Add-camera-video" autoplay playsinline></video>
            <canvas id="canvas" style="display: none;"></canvas>
            <div class="Add-map">
                <div id="map" class="Add-map-box"></div>
            </div>
            <div class="Add-camera-top">
                <a href="index.php" class="Main-card-top-back"><span class="material-symbols-outlined font-back">arrow_back</span>retour</a>
                <p id="gpsInfo" class="Add-camera-gps">GPS...</p>
            </div>
            <button id="btnCapture"  class = "Add-camera-btn Btn" disabled>GPS en cours...</button>
        </div>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>