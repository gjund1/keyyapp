<?php
require_once (__DIR__ . '/config.php');

if (isLogged()) {
    header("Location: dashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['pwd']))
        $message = "Tous les champs sont obligatoires.";
    elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))
        $message = "L'adresse email n'est pas valide.";
    elseif (strlen($_POST['pwd']) < 8)
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    elseif ($_POST['pwd'] !== ($_POST['pwd2'] ?? ''))
        $message = "Les mots de passe ne correspondent pas.";
    else {
        $name = trim($_POST["name"]);
        $email = trim($_POST["email"]);
        $pwd = password_hash($_POST["pwd"], PASSWORD_DEFAULT);

        try {
            // Création du compte non vérifié
            $sql = "INSERT INTO users (name, email, pwd_hash, email_verified) VALUES (?, ?, ?, 0)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$name, $email, $pwd]);
            $userId = $pdo->lastInsertId();

            // Génération du jeton de vérification
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);

            // Expiration dans 24 heures (calculée par MySQL)
            $stmt = $pdo->prepare("INSERT INTO account_tokens (user_id, token_hash, expires_at, token_type) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 24 HOUR), 'EMAIL_VERIFY')");
            $stmt->execute([$userId, $tokenHash]);

            // Création du lien
            $link = 'https://www.keyyapp.com/verify_email.php?token=' . urlencode($token);

            // Email
            $subject = "KeyyApp - Vérification de votre adresse email";

            $body = "
                <h2>Bienvenue sur KeyyApp !</h2>
                <p>Bonjour " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . ",</p>
                <p>Merci pour votre inscription.</p>
                <p>Pour activer votre compte, veuillez confirmer votre adresse email :</p>
                <p>
                    <a href=\"" . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . "\">
                        Vérifier mon adresse email
                    </a>
                </p>
                <p>Ce lien est valable pendant 24 heures.</p>
                <p>Si vous n'êtes pas à l'origine de cette inscription, ignorez cet email.</p>
            ";

            if (sendMail($email, $subject, $body)) {
                $message = "Inscription réussie ! Consultez votre boîte mail pour vérifier votre adresse email.";
            } else {
                error_log("Échec de l'envoi du mail de vérification pour user #" . $userId);
                $message = "Votre compte a été créé, mais l'email de vérification n'a pas pu être envoyé. Contactez l'administrateur.";
            }

        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $message = "Cette adresse email est déjà utilisée.";
            } else {
                error_log("Erreur inscription : " . $e->getMessage());
                $message = "Une erreur est survenue lors de l'inscription.";
            }
        }
    }
}
?>


// ==================================================


?>

<?php require_once(__DIR__ . '/header.php'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/login.css">
<script src="js/login.js" defer></script>

<main class="Main-map">
    <div class="Main">
        <img class="Main-title-img" src="img/favicon.png" alt="KeyyApp">
        <p class="Main-title">Créer un compte</p>
        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
    
        <div class="Main-form">
            <form method="post">
                <input type="text" class="Main-form-nom InputLogin" name="name" required placeholder="Votre nom">
                <input type="email" class="Main-form-login InputLogin" name="email" required placeholder="Adresse email">
                <!-- <small>Le mot de passe doit contenir au moins 8 caractères.</small> -->
                <div class="pwd-wrapper">
                    <!-- <p class="Main-form-pwd-oublie">8 caractères minimum</p> -->
                    <input type="password" id="pwd" class="Main-form-pwd InputLogin" name="pwd" required placeholder="Mot de passe (8 caractères min)">
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
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>