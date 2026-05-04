<?php
/**
 * Контролер для обробки швидких дій з фільмами (видалення, відновлення, додавання в обране).
 * Працює виключно через POST-запити з обов'язковою перевіркою CSRF-токена.
 */
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

$action = $_POST['do'] ?? '';
$id     = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($id === 0 || empty($action)) {
    header("Location: index.php");
    exit;
}

switch ($action) {

    case 'trash':
        $stmt = $pdo->prepare("UPDATE movies SET deleted_at = NOW() WHERE id = ?");
        $stmt->execute([$id]);

        unset($_SESSION['rand_seed']);
        header("Location: index.php");
        exit;

    case 'favorite':
        // Захист від зміни стану м'яко видалених фільмів
        $stmt = $pdo->prepare(
            "UPDATE movies SET is_favorite = 1 - is_favorite
             WHERE id = ? AND deleted_at IS NULL"
        );
        $stmt->execute([$id]);

        if ($stmt->rowCount() === 0) {
            header("Location: index.php");
            exit;
        }

        header("Location: movie.php?id=" . $id);
        exit;

    case 'restore':
        $stmt = $pdo->prepare("UPDATE movies SET deleted_at = NULL WHERE id = ?");
        $stmt->execute([$id]);

        unset($_SESSION['rand_seed']);
        header("Location: trash.php");
        exit;

    case 'delete_forever':
        $stmt = $pdo->prepare(
            "SELECT poster FROM movies WHERE id = ? AND deleted_at IS NOT NULL"
        );
        $stmt->execute([$id]);
        $movie = $stmt->fetch();

        if (!$movie) {
            header("Location: trash.php");
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM movies WHERE id = ? AND deleted_at IS NOT NULL");
        $stmt->execute([$id]);

        deletePoster($movie['poster']);

        header("Location: trash.php");
        exit;

    default:
        http_response_code(400);
        exit('Bad Request: Невідома дія.');
}