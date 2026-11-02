<?php
require_once __DIR__ . '/config.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    // Message identique, que l'adresse existe ou non
    $message = "Si cette adresse correspond à un compte, un lien de réinitialisation sera envoyé.";

    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $stmt = $pdo->prepare(
            "SELECT id, auth_provider FROM users WHERE email = ? LIMIT 1"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Les comptes Google n'ont pas de mot de passe local à réinitialiser
        if ($user && $user['auth_provider'] === 'LOCAL') {
            $token = bin2hex(random_bytes(32));
            $tokenHash = hash('sha256', $token);
            $expiresAt = date('Y-m-d H:i:s', time() + 2*3600);

            $stmt = $pdo->prepare(
                "INSERT INTO password_resets (user_id, token_hash, expires_at)
                 VALUES (?, ?, ?)"
            );
            $stmt->execute([$user['id'], $tokenHash, $expiresAt]);

            $link = 'https://www.keyyapp.com/pwd_reset.php?token='
                  . urlencode($token);

            $subject = 'KeyyApp - Réinitialisation du mot de passe';
            $body = '
                <h2>KeyyApp - Réinitialisation du mot de passe</h2>
                <p>Pour choisir un nouveau mot de passe, clique sur le lien ci-dessous.</p>
                <p><a href="' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '">
                    Réinitialiser mon mot de passe
                </a></p>
                <p>Ce lien expire dans une heure. Si vous n’avez pas demandé cette réinitialisation, ignore cet email.</p>
            ';

            if (!sendMail($email, $subject, $body)) {
                error_log('Échec email de réinitialisation pour user #' . $user['id']);
            }
        }
    }
}
?>

<?php require_once __DIR__ . '/header.php'; ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/login.css">
<script src="js/login.js" defer></script>

<main class="Main-map">
    <div class="Main">
        <div class="Main-title">
            <img class="Main-title-img" src="img/favicon.png" alt="KeyyApp">
            <p>Mot de passe oublié</p>
        </div>

        <?php if ($message !== ''): ?>
            <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="Main-form">
            <form action="pwd_forgot.php" method="post">
                <input
                    type="email"
                    name="email"
                    class="Main-form-login InputLogin"
                    required
                    placeholder="Adresse email"
                >
                <button class="Main-form-btn Btn" type="submit">
                    Recevoir le lien de réinitialisation
                </button>
            </form>
        </div>

        <div class="Main-pascompte">
            <p><a class="Main-pascompte-a" href="login.php">Retour à la connexion</a></p>
            <!-- Pas de compte ? <a class="Main-pascompte-a" href="signin.php">Crée-en un !</a> -->
        </div>

        <!-- <p><a href="login.php">Retour à la connexion</a></p> -->
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>