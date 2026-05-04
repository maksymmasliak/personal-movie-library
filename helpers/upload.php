<?php

/**
 * Валідує та готує завантажений файл постера.
 *
 * @param array $file    Елемент $_FILES['poster']
 * @param array $errors  Масив помилок (за посиланням)
 * @return string|null   Згенероване ім'я файлу або null, якщо файл не завантажено
 */
function handlePosterUpload(array $file, array &$errors): ?string
{
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if ($file['error'] === UPLOAD_ERR_INI_SIZE) {
        $errors[] = "Файл занадто великий (перевищує налаштування сервера).";
        return null;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "Помилка завантаження файлу.";
        return null;
    }

    $tmpPath = $file['tmp_name'];
    $maxSize = 5 * 1024 * 1024; // 5 MB

    $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];

    if (!is_uploaded_file($tmpPath)) {
        $errors[] = "Помилка безпеки: недійсний тимчасовий файл.";
        return null;
    }

    if ($file['size'] > $maxSize) {
        $errors[] = "Файл занадто великий. Максимум 5 МБ.";
        return null;
    }

    // Перевіряємо реальний MIME-тип через вміст файлу (захист від підміни розширення)
    $finfo        = finfo_open(FILEINFO_MIME_TYPE);
    $realMimeType = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);

    if (!array_key_exists($realMimeType, $allowedMimeTypes)) {
        $errors[] = "Недопустимий формат файлу. Дозволені лише JPG, PNG, GIF, WEBP.";
        return null;
    }

    $extension = $allowedMimeTypes[$realMimeType];
    $newName   = uniqid('poster_', true) . '.' . $extension;

    return $newName;
}

/**
 * Переміщує тимчасовий файл постера в папку uploads.
 */
function movePoster(string $tmpPath, string $newName): bool
{
    $uploadDir = __DIR__ . '/../uploads/';

    // Використовуємо basename() як захист від Path Traversal
    return move_uploaded_file($tmpPath, $uploadDir . basename($newName));
}

/**
 * Видаляє файл постера з диску.
 */
function deletePoster(?string $posterName): void
{
    if (empty($posterName)) {
        return;
    }

    // Використовуємо basename() як захист від Path Traversal
    $path = __DIR__ . '/../uploads/' . basename($posterName);

    if (file_exists($path)) {
        unlink($path);
    }
}