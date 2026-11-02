<?php
require_once(__DIR__ . '/config.php');

$message = '';
$success = false;

$token = trim($_GET['token'] ?? '');

if ($token !== '') {
    $tokenHash = hash('sha256', $token);
    $stmt = $pdo->prepare("SELECT id, user_id FROM account_tokens WHERE token_hash = ? AND token_type = 'EMAIL_VERIFY' AND expires_at > NOW() AND used_at IS NULL LIMIT 1");
    $stmt->execute([$tokenHash]);
    $verification = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($verification) {

        try {
            $pdo->beginTransaction();

            // Activation du compte
            $stmt = $pdo->prepare(" UPDATE users SET email_verified = 1 WHERE id = ?");
            $stmt->execute([$verification['user_id']]);

            // Jeton utilisé
            $stmt = $pdo->prepare("UPDATE account_tokens SET used_at = NOW() WHERE id = ?");
            $stmt->execute([$verification['id']]);

            // Invalidation des autres jetons de ce compte
            $stmt = $pdo->prepare("UPDATE account_tokens SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL");
            $stmt->execute([$verification['user_id']]);

            $pdo->commit();
            $success = true;
            $message = "Votre adresse email a été vérifiée. Votre compte KeyyApp est maintenant activé.";

        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log("Erreur vérification email : " . $e->getMessage());
            $message = "Une erreur est survenue. Veuillez réessayer.";
        }

    } else {
        $message = "Ce lien est invalide, déjà utilisé ou a expiré.";
    }

} else {
    $message = "Aucun jeton de vérification fourni.";
}

require_once(__DIR__ . '/header.php');
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/login.css">
<script src="js/login.js" defer></script>

<main class="Main-map">
    <div class="Main">

        <div class="Main-title">
            <img class="Main-title-img" src="img/favicon.png" alt="KeyyApp">
            <p><?= $success ? 'Email vérifié !' : 'Vérification email' ?></p>
        </div>

        <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>

        <?php if ($success): ?>
            <p> </p><br>
            <p><a class="Main-form-btn Btn" href="login.php">Se connecter</a></p> 
        <?php else: ?>
            <p><a href="login.php">Retour à la connexion</a></p>
        <?php endif; ?>

    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>