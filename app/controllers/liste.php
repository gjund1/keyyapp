<?php require_once(__DIR__ . '/config.php'); ?>
<?php require_once(__DIR__ . '/data.php'); ?>
<?php require_once(__DIR__ . '/header.php'); ?>

<!-- CONST PHP ->  CONST JS -->
<script>const currentUserId = <?= $_SESSION['id'] ?? 'null' ?>;</script>
<script>const data = <?= json_encode($data, JSON_UNESCAPED_UNICODE) ?>;</script>
<script>const isAdmin = <?= isAdmin() ? 'true' : 'false' ?>;</script>
<script>const isModerator = <?= isModerator() ? 'true' : 'false' ?>;</script>


<link rel="stylesheet" href="../../public/assets/css/index.css">
<script src="../../public/assets/js/index.js" defer></script>
<script src="../../public/assets/js/filter.js" defer></script>
<script src="../../public/assets/js/list.js" defer></script>

<main class="Main">
        <?php require_once(__DIR__ . '/popin.php'); ?>
        <?php require_once(__DIR__ . '/lister.php'); ?>
        <?php require_once(__DIR__ . '/filter.php'); ?>
    </div>
    <?php require_once(__DIR__ . '/button.php'); ?>
</main>

<link rel="stylesheet" href="../../public/assets/css/lister-filter.css">

<?php require_once(__DIR__ . '/footer.php'); ?>