<?php require_once(__DIR__ . '/config.php'); ?>

<?php
if (!isset($_SESSION['new_poi'])) {
    header('Location: add.php');
    exit;
}
$poi = $_SESSION['new_poi'];
?>

<?php require_once(__DIR__ . '/header.php'); ?>

<style>
    .Main-map {
        position: absolute;
        top: 40px;
        bottom: 0;
        width: 100%;
    }
</style>

<main class="Main-map">
    <div class="AddConfirm">
        <img src="<?= htmlspecialchars($poi['photo']) ?>" class="AddConfirm-photo">
        <p>latitude : <?= htmlspecialchars($poi['lat']) ?></p>
        <p>longitude : <?= htmlspecialchars($poi['lon']) ?></p>
        <p>==============</p>
        <p>ville : <?= htmlspecialchars($poi['city']) ?> (<?= htmlspecialchars($poi['cp']) ?>) -  <?= htmlspecialchars($poi['city']) ?></p>
        <p>adresse : <?= htmlspecialchars($poi['rue']) ?></p>
        <p>quartier : <?= htmlspecialchars($poi['quartier']) ?></p>
        <p>region : <?= htmlspecialchars($poi['region']) ?></p>
        
        <form action="add_save.php" method="POST">
            <button class type="submit" class="Btn">Enregistrer</button>
        </form>
    </div>
</main>

<script>
const lat = <?= $poi['lat'] ?>;
const lon = <?= $poi['lon'] ?>;
</script>

<script src="js/add_confirm.js"></script>

<?php require_once(__DIR__ . '/footer.php'); ?>