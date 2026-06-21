<?php
require_once __DIR__ . '/app/controleurs/config.php';

// Routage simple
$action = $_GET['action'] ?? 'home';
$page = $_GET['page'] ?? '';

switch ($action) {
    case 'login':
        require_once '../app/controllers/login.php';
        break;
    case 'signin':
        require_once '../app/controllers/signin.php';
        break;
    case 'logout':
        require_once '../app/controllers/logout.php';
        break;
    case 'dashboard':
        require_once '../app/controllers/dashboard.php';
        break;
    case 'liste':
        require_once '../app/controllers/list.php';
        break;
    case 'card':
        require_once '../app/controllers/card.php';
        break;
    case 'add':
        require_once '../app/controllers/add.php';
        break;
    case 'home':
    default:
        require_once '../app/controllers/index.php';
        break;
}