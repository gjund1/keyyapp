<?php
require_once __DIR__ . '/config.php';

$message = '';
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$tokenHash = '';

if ($token !== '') {
    $tokenHash = hash('sha256', $token);

    $stmt = $pdo->prepare(" SELECT id, user_id FROM account_tokens WHERE token_hash = ? AND token_type = 'PASSWORD_RESET' AND expires_at > NOW() AND used_at IS NULL LIMIT 1");
    $stmt->execute([$tokenHash]);
    $reset = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    $reset = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (!$reset) {
        $message = "Ce lien de réinitialisation est invalide ou a expiré.";
    } elseif (strlen($password) < 8) {
        $message = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($password !== $passwordConfirm) {
        $message = "Les mots de passe ne correspondent pas.";
    } else {
        $newHash = password_hash($password, PASSWORD_DEFAULT);

        $pdo->beginTransaction();

        try {
            $stmt = $pdo->prepare("
                UPDATE users
                SET pwd_hash = ?
                WHERE id = ?
            ");
            $stmt->execute([$newHash, $reset['user_id']]);

            $stmt = $pdo->prepare("
                UPDATE account_tokens
                SET used_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reset['id']]);

            // Invalide aussi les autres liens encore actifs pour ce compte
            $stmt = $pdo->prepare("
                UPDATE account_tokens
                SET used_at = NOW()
                WHERE user_id = ?
                  AND used_at IS NULL
            ");
            $stmt->execute([$reset['user_id']]);

            $pdo->commit();

            header('Location: login.php?pwd_reset=success');
            exit;

        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Erreur réinitialisation mot de passe : ' . $e->getMessage());
            $message = "Une erreur est survenue. Réessaie plus tard.";
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
            <p>Nouveau mot de passe</p>
        </div>

        <?php if (!$reset): ?>
            <p>Ce lien de réinitialisation est invalide ou a expiré.</p>
            <p><a href="pwd_forgot.php">Demander un nouveau lien</a></p>
        <?php else: ?>
            <?php if ($message !== ''): ?>
                <p><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="Main-form">
                <form action="pwd_reset.php" method="post">
                    <input type="hidden" name="token"
                           value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">

                    <input
                        type="password"
                        name="password"
                        class="Main-form-login InputLogin"
                        required
                        minlength="8"
                        placeholder="Nouveau mot de passe"
                    >

                    <input
                        type="password"
                        name="password_confirm"
                        class="Main-form-login InputLogin"
                        required
                        minlength="8"
                        placeholder="Confirmer le mot de passe"
                    >

                    <button class="Main-form-btn Btn" type="submit">
                        Modifier le mot de passe
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/footer.php'; ?>