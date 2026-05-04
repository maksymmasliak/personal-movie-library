<?php require_once 'partials/header.php'; ?>

    <main class="container">
        <a href="index.php" class="btn-back">← Повернутися до списку</a>

        <div class="form-container">
            <h2 class="form-title">Додати фільм</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <p><?= e($error) ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form action="store.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

                <div class="form-group">
                    <label for="title">Назва</label>
                    <input type="text" name="title" id="title"
                           required minlength="2" maxlength="255"
                           placeholder="Назва фільму"
                           value="<?= e($oldInput['title'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="director">Режисер</label>
                    <input type="text" name="director" id="director"
                           required minlength="2" maxlength="255"
                           placeholder="Ім'я режисера"
                           value="<?= e($oldInput['director'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="genre-select">Жанри</label>
                    <?php $selectedGenres = $oldInput['genres'] ?? []; ?>
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
                    <input type="number" id="release_year" name="release_year"
                           min="1888" max="<?= (int)date('Y') + 2 ?>" step="1" required
                           value="<?= e($oldInput['release_year'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="rating">Оцінка (0 - 10)</label>
                    <input type="number" id="rating" name="rating"
                           min="0" max="10" step="0.1"
                           placeholder="Наприклад: 7.5"
                           value="<?= e($oldInput['rating'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label>Статус: Переглянуто?</label>
                    <div class="radio-group">
                        <?php $status = $oldInput['status'] ?? '0'; ?>
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
                        <?php $fav = $oldInput['is_favorite'] ?? '0'; ?>
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
                    <textarea id="review" name="review"
                              rows="5" maxlength="5000"
                              placeholder="Ваші враження..."><?= e($oldInput['review'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="poster">Афіша</label>
                    <input type="file" id="poster" name="poster" accept="image/*">
                </div>

                <button type="submit" class="btn-submit">Зберегти фільм</button>

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