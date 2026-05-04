<?php
require_once "bootstrap.php";
/** @var PDO $pdo */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

if (!hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? '')) {
    http_response_code(403);
    die('Forbidden');
}

$id = (int)($_POST['id'] ?? 0);

if ($id === 0) {
    header("Location: index.php");
    exit;
}

// AND deleted_at IS NULL — не даємо редагувати м'яко видалені фільми
$stmt = $pdo->prepare("SELECT poster FROM movies WHERE id = ? AND deleted_at IS NULL");
$stmt->execute([$id]);
$movie = $stmt->fetch();

if (!$movie) {
    header("Location: index.php");
    exit;
}

$allGenres = require __DIR__ . '/config/genres.php';
$errors    = validateMovieFields($_POST, $allGenres);

$newPosterName = null;
if (isset($_FILES['poster'])) {
    $newPosterName = handlePosterUpload($_FILES['poster'], $errors);
}

if (!empty($errors)) {
    $_SESSION['errors']    = $errors;
    $_SESSION['old_input'] = $_POST;
    header("Location: edit.php?id=" . $id);
    exit;
}

$title        = trim($_POST['title']);
$director     = trim($_POST['director']);
$review       = trim($_POST['review'] ?? '');
$release_year = (int)$_POST['release_year'];
$status       = (int)($_POST['status'] ?? 0);
$is_favorite  = (int)($_POST['is_favorite'] ?? 0);
$genres       = $_POST['genres'];

$ratingRaw = $_POST['rating'] ?? '';
$rating    = $ratingRaw !== '' ? (float)$ratingRaw : null;

$posterToSave   = $newPosterName ?? $movie['poster'];
$posterToDelete = $newPosterName ? $movie['poster'] : null;

// real_name оновлюємо тільки якщо завантажили новий файл
$realName = $newPosterName ? basename($_FILES['poster']['name']) : null;

// Флаг: чи був новий файл фізично переміщений в uploads/.
// Потрібен для коректного очищення в catch —
// deletePoster чіпати тільки якщо файл вже там лежить.
$posterMoved = false;

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        UPDATE movies SET
            title        = ?,
            director     = ?,
            release_year = ?,
            rating       = ?,
            status       = ?,
            is_favorite  = ?,
            review       = ?,
            poster       = ?,
            real_name    = COALESCE(?, real_name)
        WHERE id = ?
    ");

    // COALESCE(?, real_name) — якщо передаємо NULL (постер не змінювався),
    // залишаємо старе значення real_name в БД
    $stmt->execute([
        $title,
        $director,
        $release_year,
        $rating,
        $status,
        $is_favorite,
        $review,
        $posterToSave,
        $realName,
        $id,
    ]);

    // Оновлюємо жанри — видаляємо всі старі і вставляємо нові.
    // ON DELETE CASCADE подбає про цілісність якщо щось піде не так.
    $pdo->prepare("DELETE FROM movie_genre WHERE movie_id = ?")->execute([$id]);

    $genreStmt = $pdo->prepare("INSERT INTO movie_genre (movie_id, genre_slug) VALUES (?, ?)");
    foreach ($genres as $slug) {
        $genreStmt->execute([$id, $slug]);
    }

    // Переміщуємо новий файл тільки після успішних запитів.
    // Якщо movePoster впаде — rollBack відкотить БД, а catch прибере файл з uploads/.
    if ($newPosterName) {
        if (!movePoster($_FILES['poster']['tmp_name'], $newPosterName)) {
            throw new \RuntimeException("Не вдалося зберегти новий постер.");
        }
        $posterMoved = true;
    }

    $pdo->commit();

    // Видаляємо старий постер з диску тільки після успішного commit.
    // Якщо робити до commit і транзакція впаде — файл вже знищено, а БД відкотилась.
    if ($posterToDelete) {
        deletePoster($posterToDelete);
    }

    unset($_SESSION['rand_seed']);
    header("Location: movie.php?id=" . $id);
    exit;

} catch (\Throwable $e) {
    $pdo->rollBack();

    // Видаляємо новий файл тільки якщо він вже був переміщений в uploads/.
    // Якщо movePoster сам і впав — файлу в uploads/ ще немає, PHP сам
    // прибере tmp_name після завершення запиту.
    if ($newPosterName && $posterMoved) {
        deletePoster($newPosterName);
    }

    error_log("Помилка update.php: " . $e->getMessage());
    $_SESSION['errors']    = ["Сталася помилка при оновленні. Спробуйте пізніше."];
    $_SESSION['old_input'] = $_POST;
    header("Location: edit.php?id=" . $id);
    exit;
}