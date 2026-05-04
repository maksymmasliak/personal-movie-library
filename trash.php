<?php
require_once "bootstrap.php";
/** @var PDO $pdo */


$stmt   = $pdo->query(
    "SELECT * FROM movies WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC"
);
$movies = $stmt->fetchAll();

// Дістаємо жанри для фільмів у кошику
$movieIds      = array_column($movies, 'id');
$genresByMovie = [];

if (!empty($movieIds)) {
    $placeholders = implode(',', array_fill(0, count($movieIds), '?'));
    $stmt = $pdo->prepare(
        "SELECT movie_id, genre_slug 
         FROM movie_genre 
         WHERE movie_id IN ($placeholders)"
    );
    $stmt->execute($movieIds);

    foreach ($stmt->fetchAll() as $row) {
        $genresByMovie[$row['movie_id']][] = $row['genre_slug'];
    }
}

$csrfToken     = $_SESSION['csrf_token'];
$currentScript = 'trash.php';
$allGenres = require __DIR__ . '/config/genres.php';
$pageTitle  = 'Кошик - Кінобібліотека';
require_once 'views/trash.view.php';