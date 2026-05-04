<?php require_once 'partials/header.php'; ?>

    <main class="container">
        <div class="form-container">
            <h2 class="form-title">Редагувати фільм</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="update.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div class="form-group">
                    <label for="title">Назва фільму</label>
                    <input type="text" name="title" id="title"
                           required minlength="2" maxlength="255"
                           value="<?= e($oldInput['title'] ?? $movie['title']) ?>">
                </div>

                <div class="form-group">
                    <label for="director">Режисер</label>
                    <input type="text" name="director" id="director"
                           required minlength="2" maxlength="255"
                           value="<?= e($oldInput['director'] ?? $movie['director']) ?>">
                </div>

                <div class="form-group">
                    <label for="genre-select">Жанри</label>
                    <?php $selectedGenres = $oldInput['genres'] ?? $currentGenres ?? []; ?>
                    <select name="genres[]" id="genre-select" multiple required>
                        <?php foreach ($allGenres as $slug => $name): ?>
                            <option value="<?= e($slug) ?>" <?= in_array($slug, $selectedGenres) ? 'selected' : '' ?>>
                                <?= e($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="release_year">Рік випуску</label>
                    <input type="number" name="release_year" id="release_year"
                           min="1888" max="<?= (int)date('Y') + 2 ?>" required
                           value="<?= e($oldInput['release_year'] ?? $movie['release_year']) ?>">
                </div>

                <div class="form-group">
                    <label for="rating">Оцінка (0 - 10)</label>
                    <input type="number" id="rating" name="rating"
                           min="0" max="10" step="0.1"
                           placeholder="Наприклад: 7.5"
                           value="<?= e($oldInput['rating'] ?? $movie['rating']) ?>">
                </div>

                <div class="form-group">
                    <label>Статус: Переглянуто?</label>
                    <div class="radio-group">
                        <?php $status = $oldInput['status'] ?? $movie['status'] ?? '0'; ?>
                        <label>
                            <input type="radio" name="status" value="1"
                                    <?= $status == '1' ? 'checked' : '' ?>> Так
                        </label>
                        <label>
                            <input type="radio" name="status" value="0"
                                    <?= $status == '0' ? 'checked' : '' ?>> Ні
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label>Додати в обране?</label>
                    <div class="radio-group">
                        <?php $fav = $oldInput['is_favorite'] ?? $movie['is_favorite'] ?? '0'; ?>
                        <label>
                            <input type="radio" name="is_favorite" value="1"
                                    <?= $fav == '1' ? 'checked' : '' ?>> Так
                        </label>
                        <label>
                            <input type="radio" name="is_favorite" value="0"
                                    <?= $fav == '0' ? 'checked' : '' ?>> Ні
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="review">Рецензія</label>
                    <textarea name="review" id="review"
                              rows="5" maxlength="5000"><?= e($oldInput['review'] ?? $movie['review']) ?></textarea>
                </div>

                <div class="form-group">
                    <label>Поточний постер</label>
                    <?php if ($movie['poster']): ?>
                        <img src="uploads/<?= e($movie['poster']) ?>"
                             class="current-poster" alt="Постер">
                    <?php else: ?>
                        <p class="text-muted">Постера немає</p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="poster">Завантажити новий постер (якщо хочете змінити)</label>
                    <input type="file" id="poster" name="poster" accept="image/*">
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit btn-submit--auto">
                        Зберегти зміни
                    </button>
                    <a href="movie.php?id=<?= (int)$movie['id'] ?>" class="btn-cancel">
                        Скасувати
                    </a>
                </div>

            </form>
        </div>
    </main>
    <!--
            Choices.js — бібліотека для стилізації <select multiple>.
            Перетворює стандартний мультиселект жанрів на зручний
            випадаючий список з пошуком і видаленням тегів.
            Ініціалізація — js/validation.js (new Choices(...))
        -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js" defer></script>

    <!-- Клієнтська валідація форми + ініціалізація Choices.js -->
    <script src="js/validation.js"></script>

<?php require_once 'partials/footer.php'; ?>