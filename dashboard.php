<?php
require_once(__DIR__ . '/config.php');
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

function nbPois($pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM pois WHERE user_id = ? AND status != 'CANCELED'");
    $stmt->execute([$_SESSION['id']]);
    return $stmt->fetchColumn();
}

function nbPoisPending($pdo) {
    $stmt = $pdo->query("SELECT COUNT(*) FROM pois WHERE status = 'PENDING'");
    return $stmt->fetchColumn();
}

function roleBadgeClass($role) {
    return match ($role) {
        "ADMIN" => "badge-admin",
        "MODERATOR" => "badge-moderator",
        "USER" => "badge-user",
        default => "badge-visitor"
    };
}

function roleBadgeName($role) {
    return match ($role) {
        "ADMIN" => "Administrateur",
        "MODERATOR" => "Moderator",
        "USER" => "Membre",
        default => "Visitor"
    };
}

?>

<?php require_once(__DIR__ . '/header.php'); ?>
<script src="js/dashboard.js" defer></script>
<link rel="stylesheet" href="css/index.css">
<link rel="stylesheet" href="css/dashboard.css">


<main class="Main Main-map">

    <div class="Main-card">
        <div class="Main-card-dash">
            <div class="Main-dash-card-logo Card-logo <?= roleUserClass(getRole()) ?>"><?= strtoupper($_SESSION["name"][0] ?? 'U') ?></div>
            
            <div class="Main-dash-card-user">
                <span id="usernameText"><?= htmlspecialchars($_SESSION["name"]) ?></span>
                <span id="editUsernameBtn" class="material-symbols-outlined font-edit">border_color</span>
            </div>

             <div class="Main-dash-card-role <?= roleBadgeClass(getRole()) ?>"><?= roleBadgeName(getRole()) ?>&nbsp;<?= $_SESSION["id"] ?></div>
            <div class="Main-dash-card-date">inscrit le <?= dateFr() ?></div>
            <div class="Main-dash-card-id">ID: <?= $_SESSION["id"] ?></div>
        </div>
        <a class="Main-card-dash-logout btn-logout" href="logout.php">Déconnexion</a>
    </div>

    <div class="Main-liste">
        <div class="Main-dash-liste">
            <a href="liste.php?mesboites=1&tri=date&city=">
            <div class="Main-list-dash">
                <h3 class="Main-dash-liste-title">Ma liste</h3>
                <div class="Main-dash-liste-box">
                    <p class="Main-dash-liste-box-list">Boites à clés (<?= nbPois($pdo) ?>)</p>
                    <p class="Main-dash-liste-box-voir">Voir</p>
                </div>
            </div>
            </a>
        </div>
    
        <?php if (isModerator() && nbPoisPending($pdo) != 0): ?>
        <div class="Main-dash-liste">
            <a href="liste.php?&pending=1&tri=date&city=">
            <div class="Main-list-dash">
                <h3 class="Main-dash-liste-title">Boites à clés à Modérer</h3>
                <div class="Main-dash-liste-box">
                    <p class="Main-dash-liste-box-list">Boites à clés (<?= nbPoisPending($pdo) ?>) - <i>en attente</i></p>
                    <p class="Main-dash-liste-box-voir">Voir</p>
                </div>
            </div>
            </a>
        </div>
        <?php endif; ?>
    
        <div class="Main-dash-liste">
            <div class="Main-list-dash">
                <h3 class="Main-dash-liste-title">Boites à clés deja Modéré</h3>
                <div class="Main-dash-liste-box">
                    <p class="Main-dash-liste-box-list">Boites à clés (47)</p>
                    <p class="Main-dash-liste-box-voir">Voir</p>
                </div>
            </div>
        </div>

        <a class="Main-dash-deconnection btn-logout" href="logout.php">Déconnexion</a>
    </div>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>