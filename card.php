<?php require_once(__DIR__ . '/header.php'); ?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/card.css">

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="js/index.js" defer></script>
<script src="js/map_fiche.js" defer></script>
<!-- <script src="js/card.js" defer></script> -->

// ==========  CARD

<main class="Main">
    <div class="Main-map Main-card">
        <div class="Main-card-top">
            <a href="liste.php" class="Main-card-top-back"><span
                    class="material-symbols-outlined font-back">arrow_back</span>Liste</a>
            <p class="Main-card-top-title">Card</p> 
            <p class="Main-card-top-count"></p>
        </div>
        <div class="Main-card-box">
            <div id="map" class="Main-card-map">
                
            </div>
            <img class="Main-card-photo" src="img/66993195266c203f1e457139.webp" alt="boites">
            <div class="Main-card-content1">
                <p>Latitude : 43.3127424</p>
                <p>Longitude : 5.4001664</p><br>
                <p>Distance :  1,4 km</p><br>
                <p>Ajouté le 19 février 2026 à 14:32</p><br><br>
                <p>Affichage : <i>Public</i></p>
                <p>Statut : <span class="Main-card-content1-status-span">Validé</span></p>
            </div>
            <div class="Main-card-content2">
                <div class="Main-card-content2-adresse">
                    <p class="Main-card-content2-city"><span class="Main-card-content2-city-span">Marseilles</span> (13002)</p>
                    <p class="Main-card-content2-dep">Bouches-du-Rhône (FR)</p>
                    <p class="Main-card-content2-street">Rue Ferdinand Rey</p>
                    <p class="Main-card-content2-quartier">Quartier : La Plaine</p>
                </div>
                <div class="Main-card-content2-coment">
                    <p>comment : 3 boîtes à clés accrochées au local à velo.</p>
                </div>
                <div class="Main-card-content2-btn">
                    <button class="Main-card-modify Btn">Modifier</button>
                    <button class="Main-card-btn-del Btn">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

</main>


// ==================
<?php require_once(__DIR__ . '/footer.php'); ?>