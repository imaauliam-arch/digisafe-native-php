<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
if (!empty($_SESSION['admin_id'])) log_activity($pdo, 'admin_logout', 'Admin keluar dari sistem', 'admin', (int)$_SESSION['admin_id']);
$_SESSION = [];
if (ini_get('session.use_cookies')) { $params = session_get_cookie_params(); setcookie(session_name(), '', time()-42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']); }
session_destroy();
header('Location: login.php'); exit;
