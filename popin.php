<link rel="stylesheet" href="css/popin.css">

<?php if (!empty($_SESSION['success'])): ?>
    <div id="toast" class="toast-success"><?= htmlspecialchars($_SESSION['success']) ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>