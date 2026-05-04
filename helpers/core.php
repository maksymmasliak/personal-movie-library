<?php

/**
 * Безпечний вивід рядка в HTML.
 * @param string|null $string Рядок для екранування
 * @return string Безпечний рядок
 */
function e(?string $string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Повертає масив жанрів фільму у вигляді рядка для виводу.
 * Наприклад: ['action', 'drama'] → 'Бойовик, Драма'
 *
 * @param array<int, string> $slugs Масив slug-ів жанрів фільму
 * @param array<string, string> $allGenres Повний масив жанрів з config/genres.php
 * @return string Відформатований рядок жанрів, розділений комами
 */
function formatGenres(array $slugs, array $allGenres): string {
    $names = [];
    foreach ($slugs as $slug) {
        if (isset($allGenres[$slug])) {
            $names[] = $allGenres[$slug];
        }
    }
    return implode(', ', $names);
}

/**
 * Дістає slugs жанрів конкретного фільму з БД.
 * Повертає простий масив рядків: ['action', 'drama']
 *
 * @param PDO $pdo Екземпляр підключення до БД
 * @param int $movieId ID фільму
 * @return array<int, string> Масив slug-ів жанрів
 */
function getMovieGenres(PDO $pdo, int $movieId): array {
    $stmt = $pdo->prepare("SELECT genre_slug FROM movie_genre WHERE movie_id = ?");
    $stmt->execute([$movieId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}