<?php
require_once "bootstrap.php";
/** @var PDO $pdo */

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id === 0) {
    header("Location: index.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM movies WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    header("Location: index.php");
    exit;
}

$currentGenres = $_SESSION['old_input']['genres'] ?? getMovieGenres($pdo, $id);
$allGenres     = require __DIR__ . '/config/genres.php';

$errors   = $_SESSION['errors'] ?? [];
$oldInput = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']);

$csrfToken     = $_SESSION['csrf_token'];
$currentScript = 'edit.php';
$pageTitle     = 'Редагувати: ' . e($movie['title']);
require_once 'views/edit.view.php';