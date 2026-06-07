<?php
require_once(__DIR__ . '/config.php');

if (isLogged()) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $pwd = $_POST["pwd"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pwd, $user["pwd_hash"])) {
        session_regenerate_id(true);

        $_SESSION["id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["email"] = $user["email"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["created_at"] = $user["created_at"];

        header("Location: dashboard.php");
        exit();

    } else
        $message = "Email ou mot de passe incorrect";

    $stmt = $pdo->prepare("UPDATE users SET last_seen_at = NOW() WHERE id = ?");
    $stmt->execute([$user["id"]]);
}

?>

<?php require_once(__DIR__ . '/header.php'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/login.css">
<script src="js/login.js" defer></script>

<main class="Main-map">
    <div class="Main">
        <div class="Main-title">
            <?php if (isset($_GET['register']) && $_GET['register'] === 'success'): ?>
                <div class="toast-success">Compte créé avec succès !</div>
            <?php endif; ?>
            <img class="Main-title-img" src="img/favicon.png" alt="KeyyApp">
            <p>Connectez-vous à votre compte</p>
        </div>
        <p><?= $message ?></p>
    
        <div class="Main-form">
            <form action="login.php" method="post">
                <input type="email" name="email" class="Main-form-login InputLogin" required placeholder="Adresse email"><br>
                <div class="pwd-wrapper">
                    <input type="password" id="pwd" name="pwd" class="Main-form-pwd InputLogin" required placeholder="Mot de passe"><br>
                    <p class="Main-form-pwd-oublie">Mot de passe oublié</p>
                    <span class="togglePwd pwd-oeil" data-target="pwd"><i class="fa-solid fa-eye"></i></span>
                </div>
                <button class="Main-form-btn Btn" type="submit">Se connecter</button>
            </form>
        </div>
    
        <div class="Main-google"></div>
    
        <div class="Main-pascompte">
            Pas de compte ? <a class="Main-pascompte-a" href="signin.php">Crée-en un !</a>
        </div>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>