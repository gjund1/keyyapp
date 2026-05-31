<?php
require_once (__DIR__ . '/config.php');

if (isLogged()) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (name, email, pwd_hash) VALUES (?, ?, ?)";
    $stmt = $pdo->prepare($sql);

    try {
        $stmt->execute([$name, $email, $pwd]);
        header("Location: login.php?register=success");
        exit();

    } catch(PDOException $e) {
        $message = "Email déjà utilisé !";
        // $message = $e->getMessage();
    }
}
?>

<?php require_once(__DIR__ . '/header.php'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/login.css">
<script src="js/login.js" defer></script>

<main class="Main-map Main">
    <div class="Main-title">
        Créer un compte
    </div>
    <p><?= $message ?></p>

    <div class="Main-form">
        <form method="post">
            <input type="text" class="Main-form-nom InputLogin" name="name" required placeholder="Votre nom">
            <input type="email" class="Main-form-login InputLogin" name="email" required placeholder="Adresse email">
            <div class="pwd-wrapper">
                <input type="password" id="pwd" class="Main-form-pwd InputLogin" name="pwd" required placeholder="Mot de passe">
                <span class="togglePwd" data-target="pwd"><i class="fa-solid fa-eye"></i></span>
            </div>

            <div class="pwd-wrapper">
                <input type="password" id="pwd2" class="Main-form-pwd2 InputLogin" name="pwd2" required placeholder="Confirmation du mot de passe">
                <span class="togglePwd" data-target="pwd2"><i class="fa-solid fa-eye"></i></span>
            </div>
            <button id="submitBtn" class="Main-form-btn Btn" type="submit" disabled>Envoyer</button>
        </form>
    </div>

    <div class="Main-google"></div>

    <div class="Main-pascompte">
        Vous avez déjà un compte ? <a class="Main-pascompte-a" href="login.php">Se connecter</a>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>