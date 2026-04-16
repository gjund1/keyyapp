<link rel="stylesheet" href="css/button.css">
<script src="js/button.js" defer></script>


<!-- MAP Button -->
<div class="Main-button">
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') : ?>
    <div>
        <div class="Main-button-locate">📍</div>
        <a href="liste.php" class="Main-button-list">📋</a>
    </div>
    <?php else : ?>
        <a href="index.php" class="Main-button-list"><span class="material-symbols-outlined font-map">map</span></a>
    <?php endif; ?>
    <a href="add.php" class="Main-button-add">+</a>
</div>