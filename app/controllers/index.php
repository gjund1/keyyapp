<?php require_once(__DIR__ . '/config.php'); ?>
<?php require_once(__DIR__ . '/data.php'); ?>
<?php require_once(__DIR__ . '/header.php'); ?>

<!-- CONST PHP ->  CONST JS -->
<script>const currentUserId = <?= $_SESSION['id'] ?? 'null' ?>;</script>
<script>const data = <?= json_encode($data, JSON_UNESCAPED_UNICODE) ?>;</script>

<link rel="stylesheet" href="../../public/assets/css/index.css">
<script src="../../public/assets/js/filter.js" defer></script>
<script src="../../public/assets/js/index.js" defer></script>

<main class="Main">
    <?php require_once(__DIR__ . '/map.php'); ?>
    <?php require_once(__DIR__ . '/button.php'); ?>
    <?php require_once(__DIR__ . '/filter.php'); ?>
</main>

<?php require_once(__DIR__ . '/footer.php'); ?>