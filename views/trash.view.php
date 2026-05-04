<?php require_once 'partials/header.php'; ?>

    <main class="container">

        <h1 class="page-title">Кошик</h1>

        <?php if (!empty($movies)): ?>
            <div class="trash-grid">
                <?php foreach ($movies as $movie): ?>
                    <div class="trash-card">

                        <div class="poster-wrapper">
                            <?php if ($movie['poster']): ?>
                                <img src="uploads/<?= e($movie['poster']) ?>"
                                     alt="<?= e($movie['title']) ?>">
                            <?php else: ?>
                                <img src="uploads/default.svg" alt="Немає постера">
                            <?php endif; ?>
                        </div>

                        <div class="trash-card-info">
                            <h2 class="trash-card-title"><?= e($movie['title']) ?></h2>
                            <p class="trash-card-meta">
                                <?= e($movie['director']) ?>, <?= (int)$movie['release_year'] ?>
                            </p>
                            <?php $slugs = $genresByMovie[$movie['id']] ?? []; ?>
                            <?php if (!empty($slugs)): ?>
                                <p class="trash-card-meta">
                                    <?= e(formatGenres($slugs, $allGenres)) ?>
                                </p>
                            <?php endif; ?>
                            <p class="trash-card-date">
                                Видалено: <?= date('d.m.Y H:i', strtotime($movie['deleted_at'])) ?>
                            </p>
                        </div>

                        <div class="trash-card-actions">

                            <form action="action.php" method="post" class="form-inline">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="do" value="restore">
                                <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
                                <button type="submit" class="btn-action btn-restore">
                                    Відновити
                                </button>
                            </form>

                            <form action="action.php" method="post" class="form-inline delete-forever-form">
                                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                                <input type="hidden" name="do" value="delete_forever">
                                <input type="hidden" name="id" value="<?= (int)$movie['id'] ?>">
                                <button type="submit" class="btn-action btn-delete-forever">
                                    Видалити назавжди
                                </button>
                            </form>

                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="movies-empty">Кошик порожній</p>
        <?php endif; ?>

        <a href="index.php" class="btn-back">← Повернутися до списку</a>

    </main>

<?php require_once 'partials/footer.php'; ?>