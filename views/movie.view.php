<?php require_once 'partials/header.php'; ?>

    <main class="container">
        <a href="index.php" class="btn-back">← Повернутися до списку</a>

        <div class="single-movie-container">

            <div class="single-poster">
                <?php if ($movie['poster']): ?>
                    <img src="uploads/<?= e($movie['poster']) ?>"
                         alt="<?= e($movie['title']) ?>">
                <?php else: ?>
                    <img src="uploads/default.svg" alt="Немає постера">
                <?php endif; ?>
            </div>

            <div class="single-info">
                <h1>
                    <?= e($movie['title']) ?>
                    <img src="icons/<?= $movie['is_favorite'] ? 'heart-red.svg' : 'heart-white.svg' ?>"
                         alt="<?= $movie['is_favorite'] ? 'Улюблене' : 'Не улюблене' ?>"
                         class="icon-heart">
                </h1>

                <div class="info-row">
                    <strong>Режисер:</strong> <?= e($movie['director']) ?>
                </div>

                <div class="info-row">
                    <strong>Рік випуску:</strong> <?= (int)$movie['release_year'] ?>
                </div>

                <div class="info-row">
                    <strong>Жанр:</strong>
                    <?php if (!empty($movieGenres)): ?>
                        <?= e(formatGenres($movieGenres, $allGenres)) ?>
                    <?php else: ?>
                        <span class="text-muted">не вказано</span>
                    <?php endif; ?>
                </div>

                <div class="info-row">
                    <strong>Оцінка:</strong>
                    <?php if ($movie['rating'] !== null): ?>
                        ⭐ <?= number_format((float)$movie['rating'], 1) ?> / 10
                    <?php else: ?>
                        <span class="text-muted">не вказана</span>
                    <?php endif; ?>
                </div>

                <div class="info-row">
                    <strong>Статус:</strong>
                    <?= $movie['status'] ? 'Переглянуто' : 'Не переглянуто' ?>
                </div>

                <?php if ($movie['review']): ?>
                    <div class="review-box">
                        "<?= nl2br(e($movie['review'])) ?>"
                    </div>
                <?php endif; ?>

                <div class="action-buttons">

                    <form action="action.php" method="post" class="form-inline">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="do" value="favorite">
                        <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
                        <button type="submit" class="btn-action btn-fav">
                            <?= $movie['is_favorite'] ? 'Видалити з улюблених' : 'Додати в улюблені' ?>
                        </button>
                    </form>

                    <a href="edit.php?id=<?= (int)$movie['id'] ?>" class="btn-action btn-edit">
                        Редагувати
                    </a>

                    <form action="action.php" method="post" class="form-inline" id="trash-form">
                        <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                        <input type="hidden" name="do" value="trash">
                        <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
                        <button type="submit" class="btn-action btn-trash">
                            Видалити
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </main>

<?php require_once 'partials/footer.php'; ?>