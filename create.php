<?php
require_once "bootstrap.php";

$errors   = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']);

$allGenres     = require __DIR__ . '/config/genres.php';
$csrfToken     = $_SESSION['csrf_token'];
$currentScript = 'create.php';
$pageTitle     = 'Додати фільм - Кінобібліотека';
require_once 'views/create.view.php';