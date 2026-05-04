<?php
require_once "bootstrap.php";
/** @var PDO $pdo */

$isFiltered = !empty($_GET['genre'])
    || (!empty($_GET['sort']) && $_GET['sort'] !== 'default')
    || !empty($_GET['is_favorite']);

$movies      = [];
$totalPages  = 0;
$currentPage = 1;
$perPage     = 10;

if ($isFiltered) {

    $conditions = ["m.deleted_at IS NULL"];
    $params     = [];

    // Фільтр по жанру — шукаємо через EXISTS з movie_genre
    if (!empty($_GET['genre'])) {
        $conditions[] = "EXISTS (
            SELECT 1 FROM movie_genre mg
            WHERE mg.movie_id = m.id AND mg.genre_slug = :genre
        )";
        $params[':genre'] = $_GET['genre'];
    }

    if (!empty($_GET['is_favorite']) && $_GET['is_favorite'] == '1') {
        $conditions[] = "m.is_favorite = 1";
    }

    $whereSql = "WHERE " . implode(' AND ', $conditions);

    // Whitelist сортування — захист від SQL-ін'єкції через GET-параметр
    $allowedSorts = [
        'year_desc'   => 'm.release_year DESC',
        'year_asc'    => 'm.release_year ASC',
        'rating_desc' => 'm.rating DESC',
        'default'     => 'm.id DESC',
    ];
    $sortKey  = $_GET['sort'] ?? 'default';
    $orderSql = $allowedSorts[$sortKey] ?? 'm.id DESC';


    $countSql  = "SELECT COUNT(*) FROM movies m $whereSql";
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $total      = (int)$countStmt->fetchColumn();
    $totalPages = (int)ceil($total / $perPage);

    if ($totalPages > 0) {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        elseif ($currentPage > $totalPages) $currentPage = $totalPages;

        $offset = ($currentPage - 1) * $perPage;

        // LIMIT і OFFSET не можна передати через named placeholders разом з іншими —
        // використовуємо bindValue з явним типом PDO::PARAM_INT
        $sql  = "SELECT m.* FROM movies m $whereSql ORDER BY $orderSql LIMIT :limit OFFSET :offset";
        $stmt = $pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $movies = $stmt->fetchAll();
    }

} else {

    // Генеруємо seed один раз за сесію.
    // RAND(seed) дає стабільний рандомний порядок в межах однієї сесії,
    // але різний після додавання/видалення фільму.
    if (!isset($_SESSION['rand_seed'])) {
        $_SESSION['rand_seed'] = rand(1, 999999);
    }
    $seed = $_SESSION['rand_seed'];

    $total      = (int)$pdo->query("SELECT COUNT(*) FROM movies WHERE deleted_at IS NULL")->fetchColumn();
    $totalPages = (int)ceil($total / $perPage);

    if ($totalPages > 0) {
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($currentPage < 1) $currentPage = 1;
        elseif ($currentPage > $totalPages) $currentPage = $totalPages;

        $offset = ($currentPage - 1) * $perPage;

        $stmt = $pdo->prepare(
            "SELECT * FROM movies
             WHERE deleted_at IS NULL
             ORDER BY RAND(:seed)
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':seed',   $seed,    PDO::PARAM_INT);
        $stmt->bindValue(':limit',  $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset,  PDO::PARAM_INT);
        $stmt->execute();
        $movies = $stmt->fetchAll();
    }
}


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


$selectedGenre = $_GET['genre']      ?? '';
$selectedSort  = $_GET['sort']       ?? 'default';
$selectedFav   = $_GET['is_favorite'] ?? '';

$allGenres     = require __DIR__ . '/config/genres.php';
$csrfToken     = $_SESSION['csrf_token'];
$currentScript = 'index.php';
$pageTitle     = 'Головна - кіноколекція';
require_once 'views/index.view.php';