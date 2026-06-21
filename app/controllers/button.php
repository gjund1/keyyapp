<link rel="stylesheet" href="../../public/assets/css/button.css">
<script src="../../public/assets/js/button.js" defer></script>


<!-- MAP Button -->
<div class="Main-button">
    <?php if (basename($_SERVER['PHP_SELF']) == 'index.php') : ?>
    <div>
        <div class="Main-button-locate"><span class="material-symbols-outlined font-map">my_location</span></div>
        <a href="liste.php" class="Main-button-list"><span class="material-symbols-outlined font-map">format_list_bulleted</span></a>
    </div>
    <?php else : ?>
        <a href="index.php" class="Main-button-list"><span class="material-symbols-outlined font-map">map</span></a>
    <?php endif; ?>
    <a href="add.php" class="Main-button-add"><span class="font-add">+</span></a>
</div>