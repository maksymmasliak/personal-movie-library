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

$movieGenres = getMovieGenres($pdo, $id);
$allGenres   = require __DIR__ . '/config/genres.php';

$csrfToken     = $_SESSION['csrf_token'];
$currentScript = 'movie.php';
$pageTitle     = e($movie['title']) . ' - Кінобібліотека';
require_once 'views/movie.view.php';