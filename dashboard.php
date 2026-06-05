<?php
require_once(__DIR__ . '/config.php');

// // Réservé aux membres :
requireLogin();

function dateFr() {
    if (empty($_SESSION["created_at"]))
        return "Date inconnue";
    $dt = new DateTime($_SESSION["created_at"]);
    $day = (int)$dt->format('j');
    $month = strftime('%B', $dt->getTimestamp());
    $year = $dt->format('Y');
    return ($day === 1 ? '1er' : $day) . " $month $year";
}

?>

<?php require_once(__DIR__ . '/header.php'); ?>
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/dashboard.css">


<main class="Main Main-map">

    <div class="Main-card">
        <div class="Main-card-dash">
            <div class="Main-dash-card-logo"><?= strtoupper($_SESSION["name"][0]) ?></div>
            <div class="Main-dash-card-user">&nbsp;&nbsp;<?= $_SESSION["name"] ?> <span class="material-symbols-outlined font-edit">border_color</span></div>
            <div class="Main-dash-card-role"><?= $_SESSION["role"] ?></div>
            <div class="Main-dash-card-date">inscrit le <?= dateFr() ?></div>
            <div class="Main-dash-card-id">ID: <?= $_SESSION["id"] ?></div>
        </div>
    </div>

    <div class="Main-liste">
        <div class="Main-dash-liste">
            <a href="liste.php?mesboites=1&tri=date&city=">
            <div class="Main-list-dash">
                <h3 class="Main-dash-liste-title">Ma liste</h3>
                <div class="Main-dash-liste-box">
                    <p class="Main-dash-liste-box-list">Boites à clés (3)</p>
                    <p class="Main-dash-liste-box-voir">Voir</p>
                </div>
            </div>
            </a>
        </div>
    
        <div class="Main-dash-liste">
            <div class="Main-list-dash">
                <h3 class="Main-dash-liste-title">Boites à clés à Modérer</h3>
                <div class="Main-dash-liste-box">
                    <p class="Main-dash-liste-box-list">Boites à clés (2) - <span>en attente</span></p>
                    <p class="Main-dash-liste-box-voir">Voir</p>
                </div>
            </div>
        </div>
    
        <div class="Main-dash-liste">
            <div class="Main-list-dash">
                <h3 class="Main-dash-liste-title">Boites à clés deja Modéré</h3>
                <div class="Main-dash-liste-box">
                    <p class="Main-dash-liste-box-list">Boites à clés (47)</p>
                    <p class="Main-dash-liste-box-voir">Voir</p>
                </div>
            </div>
        </div>

        <a class="Main-dash-deconnection" href="logout.php">Déconnexion</a>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>