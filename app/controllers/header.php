<?php
function roleUserClass($role) {
    return match ($role) {
        "ADMIN" => "user-admin",
        "MODERATOR" => "user-moderator",
        "USER" => "user-user",
        default => "user-visitor"
    };
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="icon" type="image/png" sizes="32x32" href="../../public/assets/img/favicon.png"> -->
    <link rel="icon" href="../../public/assets/img/favicon.ico" type="image/x-icon">
    <meta name="description" content="Recenser et visualiser les boîtes à clés observés dans l’espace public.">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/header.css">
    <script src="../../public/assets/js/header.js" defer></script>
    <!-- <script src="../../public/assets/js/data.js"></script> -->

    <title>KeyMap</title>
</head>

<body>
    <!-- Header Nav-->
    <header class="Header">
        <div class="Header-menu">☰</div>
        <a href="index.php" class="Header-logo">Keyy<span>App</span></a>
        <div class="Header-laptop">
            <a href="dashboard.php" class="Header-laptop-item">Mon profil</a>
            <a href="liste.php" class="Header-laptop-item">Ma liste</a>
            <a href="faq.php" class="Header-laptop-item">FAQ</a>
            <!-- <a href="mentions.php" class="Menu-item">Mentions légales</a> -->
            <a href="contact.php" class="Header-laptop-item">Contact</a>
        </div>
        <div class="Header-avatar Card-logo <?= roleUserClass(getRole()) ?>">
            <a class="Card-logo-a" href="login.php"><?= strtoupper($_SESSION["name"][0] ?? 'U') ?></a>
        </div>
    </header>

 <!-- MENU -->
    <nav class="Menu">
        <div class="Menu-header">
            <div></div>
            <div class="Menu-header-title">MENU</div>
            <div class="Menu-header-closeBtn">✖</div>
        </div>
        <div class="Menu-content">
            <a href="dashboard.php" class="Menu-item">👤 Mon profil</a>
            <a href="liste.php" class="Menu-item">📋 Ma liste</a>
            <a href="faq.php" class="Menu-item">❓ FAQ</a>
            <a href="mentions.php" class="Menu-item">📄 Mentions légales</a>
            <a href="contact.php" class="Menu-item">✉️ Contact</a>
            <div class="Menu-separator"></div>
            <a href="logout.php" class="Menu-logout">❌ Déconnexion</a>
        </div>
    </nav>