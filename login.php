<?php session_start(); ?>
<?php require_once(__DIR__ . '/header.php'); ?>

<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/login.css">
<script src="js/login.js" defer></script>

<main class="Main-map Main">
    <div class="Main-title">
        <!-- <img class="Main-title-img" src="img/favicon.png" alt="KeyyApp"> -->
        <p>Connectez-vous à votre compte</p>
    </div>

    <div class="Main-form">
        <form action="login.php" method="post">
            <input type="text" class="Main-form-login" name="login" required placeholder="Adresse email"><br>
            <div class="Main-form-pwd-wrapper">
                <input type="password" class="Main-form-pwd" name="mot_de_passe" required placeholder="Mot de passe"><br>
                <p class="Main-form-pwd-oublie">Mot de passe oublié</p>
            </div>
            <button class="Main-form-btn Btn" type="submit">Se connecter</button>
        </form>
    </div>

    <div class="Main-google"></div>

    <div class="Main-pascompte">
        Pas de compte ? <a class="Main-pascompte-a" href="signin.php">Crée-en un !</a>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>