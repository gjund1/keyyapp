<link rel="stylesheet" href="../../public/assets/css/popin.css">

<?php if (!empty($_SESSION['success'])): ?>
    <div id="toast" class="toast-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>