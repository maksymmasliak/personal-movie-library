<?php

/**
 * Валідує основні поля форми фільму.
 *
 * @param array $post      масив $_POST
 * @param array $allGenres дозволені жанри з config/genres.php
 * @return array           масив помилок, порожній якщо все ок
 */
function validateMovieFields(array $post, array $allGenres): array
{
    $errors = [];

    $title    = trim($post['title'] ?? '');
    $director = trim($post['director'] ?? '');

    // Використовуємо mb_strlen для коректної роботи з кирилицею
    if (mb_strlen($title) < 2 || mb_strlen($director) < 2) {
        $errors[] = "Заповніть усі обов'язкові поля (мінімум 2 символи).";
    }

    $genres = $post['genres'] ?? [];
    if (empty($genres) || !is_array($genres)) {
        $errors[] = "Оберіть хоча б один жанр.";
    } else {
        // Захист від підробленого POST-запиту зі стороннім slug-ом
        foreach ($genres as $slug) {
            if (!array_key_exists($slug, $allGenres)) {
                $errors[] = "Один або декілька жанрів не існують.";
                break;
            }
        }
    }

    $ratingRaw = $post['rating'] ?? '';
    if ($ratingRaw !== '') {
        $rating = (float)$ratingRaw;
        if ($rating < 0 || $rating > 10) {
            $errors[] = "Оцінка має бути від 0 до 10.";
        }
    }

    $currentYear  = (int)date('Y');
    $release_year = (int)($post['release_year'] ?? 0);
    if ($release_year < 1888 || $release_year > ($currentYear + 2)) {
        $errors[] = "Рік випуску має бути між 1888 та " . ($currentYear + 2) . ".";
    }

    return $errors;
}