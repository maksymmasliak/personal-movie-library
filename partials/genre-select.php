<?php
/**
 * Партіал для рендеру списку жанрів у <select> фільтра на головній.
 * Очікує змінні від батьківського файлу.
 *
 * @var string|null $selectedGenre Поточно вибраний slug або порожній рядок
 * @var string|null $allOption     Текст першого порожнього пункту
 * @var array<string, string> $allGenres Масив жанрів з config/genres.php
 */

$allOption = $allOption ?? '-- Оберіть --';
?>
    <option value=""><?= e($allOption) ?></option>
<?php foreach ($allGenres as $value => $label): ?>
    <option value="<?= e($value) ?>"
            <?= ($selectedGenre ?? '') === $value ? 'selected' : '' ?>>
        <?= e($label) ?>
    </option>
<?php endforeach; ?>