<?php

// utilisateur déjà connecté
function isLogged(): bool {
    return isset($_SESSION['id']);
}

// Réservé aux membres
function getRole(): string {
    return $_SESSION['role'] ?? 'ANONYMOUS';
}

// Réservé aux admins
function isAdmin(): bool {
    return getRole() === 'ADMIN';
}

// Réservé aux modérateurs
function isModerator(): bool {
    return in_array(getRole(), ['ADMIN', 'MODERATEUR']);
}

// Utisisateur logé
function requireLogin(): void {
    if (!isLogged()) {
        header('Location: login.php');
        exit();
    }
}

function requireAdmin(): void {
    if (!isAdmin()) {
        http_response_code(403);
        die('Accès refusé');
    }
}

function requireModerator(): void {
    if (!isModerator()) {
        http_response_code(403);
        die('Accès refusé');
    }
}