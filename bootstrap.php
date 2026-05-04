<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers/core.php';
require_once __DIR__ . '/helpers/validation.php';
require_once __DIR__ . '/helpers/upload.php';