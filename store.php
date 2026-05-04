<?php
require_once "bootstrap.php";
/** @var PDO $pdo */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Forbidden');
}

$allGenres = require __DIR__ . '/config/genres.php';
$errors    = validateMovieFields($_POST, $allGenres);

// handlePosterUpload тільки перевіряє файл і повертає нове ім'я —
// фізичне переміщення відбувається після успішних INSERT-ів.
$newPosterName = null;
if (isset($_FILES['poster'])) {
    $newPosterName = handlePosterUpload($_FILES['poster'], $errors);
}

if (!empty($errors)) {
    $_SESSION['errors']    = $errors;
    $_SESSION['old_input'] = $_POST;
    header('Location: create.php');
    exit;
}

$title        = trim($_POST['title']);
$director     = trim($_POST['director']);
$review       = trim($_POST['review'] ?? '');
$release_year = (int)$_POST['release_year'];
$status       = (int)($_POST['status'] ?? 0);
$is_favorite  = (int)($_POST['is_favorite'] ?? 0);
$realName     = $newPosterName ? basename($_FILES['poster']['name']) : null;


$ratingRaw = $_POST['rating'] ?? '';
$rating    = $ratingRaw !== '' ? (float)$ratingRaw : null;

// validateMovieFields вже перевірив що масив не порожній і всі slug-и валідні
$genres = $_POST['genres'];

// Флаг: чи був файл фізично переміщений в uploads/.
// Потрібен для коректного очищення в catch —
// deletePoster чіпати тільки якщо файл вже там лежить.
$posterMoved = false;

try {

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO movies 
            (title, director, release_year, rating, status, is_favorite, review, poster, real_name) 
        VALUES 
            (:title, :director, :release_year, :rating, :status, :is_favorite, :review, :poster, :real_name)
    ");

    $stmt->execute([
        'title'        => $title,
        'director'     => $director,
        'release_year' => $release_year,
        'rating'       => $rating,
        'status'       => $status,
        'is_favorite'  => $is_favorite,
        'review'       => $review,
        'poster'       => $newPosterName,
        'real_name'    => $realName,
    ]);

    $movieId = (int)$pdo->lastInsertId();


    $genreStmt = $pdo->prepare("INSERT INTO movie_genre (movie_id, genre_slug) VALUES (?, ?)");
    foreach ($genres as $slug) {
        $genreStmt->execute([$movieId, $slug]);
    }

    // Переміщуємо файл тільки після успішних INSERT-ів.
    // Якщо movePoster впаде — rollBack відкотить БД, а catch прибере файл з uploads/.
    if ($newPosterName) {
        if (!movePoster($_FILES['poster']['tmp_name'], $newPosterName)) {
            throw new \RuntimeException("Не вдалося зберегти файл постера.");
        }
        $posterMoved = true;
    }

    $pdo->commit();

    // Скидаємо seed щоб новий фільм потрапив у рандомний порядок на головній
    unset($_SESSION['rand_seed']);

    header('Location: index.php');
    exit;

} catch (\Throwable $e) {
    $pdo->rollBack();

    // Видаляємо файл тільки якщо він вже був переміщений в uploads/.
    // Якщо movePoster сам і впав — файлу в uploads/ ще немає, PHP сам
    // прибере tmp_name після завершення запиту.
    if ($newPosterName && $posterMoved) {
        deletePoster($newPosterName);
    }

    error_log("Помилка store.php: " . $e->getMessage());
    $_SESSION['errors']    = ["Сталася помилка при збереженні. Спробуйте пізніше."];
    $_SESSION['old_input'] = $_POST;
    header('Location: create.php');
    exit;
}