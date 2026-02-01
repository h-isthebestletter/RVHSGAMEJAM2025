<?php
error_reporting(E_ALL ^ E_WARNING);
header('Strict-Transport-Security: max-age=63072000; includeSubDomains; preload');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: worker-src https:');

// retrieving ?filename=... from url
$filename = $_GET['filename'] ?? '';

// site-wide session
session_start();

switch ($filename) {
    case 'login':
        // session_start and session_destroy must be before header
        include 'templates/login.tpl.php';
    break;
    case 'gallery':
        include 'templates/defaults/header.tpl.php';
        include 'templates/gallery.tpl.php';
        include 'templates/defaults/end.tpl.php';
    break;
    case 'game':
        include 'backend/gameBackend.inc.php';
        include 'templates/defaults/header.tpl.php';
        include 'templates/game.tpl.php';
        include 'templates/defaults/end.tpl.php';
    break;
    default:
        include 'templates/defaults/header.tpl.php';
        include 'templates/home.tpl.php';
        include 'templates/defaults/end.tpl.php';
}
