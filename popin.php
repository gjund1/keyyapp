<link rel="stylesheet" href="css/popin.css">

<?php if (!empty($_SESSION['success'])): ?>
    <div class="toast-success"> <?= htmlspecialchars($_SESSION['success']) ?></div>
<?php endif; ?>