<?php
require_once(__DIR__ . '/config.php');

// // Réservé aux membres :
requireLogin();
?>

<?php require_once(__DIR__ . '/header.php'); ?>
<link rel="stylesheet" href="css/index.css">

<br><br><br>
<h1>Bienvenue <?= $_SESSION["name"] ?></h1>
<br>

<ul>
    <li><a href="index.php">Carte</a></li>
    <li><a href="liste.php">Liste</a></li>
    <li><a href="logout.php">Déconnexion</a></li>
</ul>

<?php require_once(__DIR__ . '/footer.php'); ?>