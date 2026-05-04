<?php require_once 'partials/header.php'; ?>

    <main class="container">

        <div class="filter-panel">
            <form action="index.php" method="get" class="filter-form">

                <div class="filter-group">
                    <label for="genre">Жанр:</label>
                    <select name="genre" id="genre">
                        <?php
                        // $selectedGenre і $allOption передані контролером (index.php),
                        // view не читає $_GET напряму
                        $allOption = 'Усі жанри';
                        require __DIR__ . '/../partials/genre-select.php';
                        ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="sort">Сортувати за:</label>
                    <select name="sort" id="sort">
                        <option value="default">За замовчуванням</option>
                        <option value="year_desc"
                                <?= $selectedSort === 'year_desc'   ? 'selected' : '' ?>>
                            Рік (Новіші спочатку)
                        </option>
                        <option value="year_asc"
                                <?= $selectedSort === 'year_asc'    ? 'selected' : '' ?>>
                            Рік (Старіші спочатку)
                        </option>
                        <option value="rating_desc"
                                <?= $selectedSort === 'rating_desc' ? 'selected' : '' ?>>
                            Оцінка (Найкращі)
                        </option>
                    </select>
                </div>

                <div class="filter-group checkbox-group">
                    <label>
                        <input type="checkbox" name="is_favorite" value="1"
                                <?= $selectedFav == '1' ? 'checked' : '' ?>>
                        <img src="icons/heart-red.svg" alt="Улюблене" class="icon-heart">
                        Тільки улюблені
                    </label>
                </div>

                <button type="submit" class="btn-filter">Застосувати</button>
                <a href="index.php" class="btn-reset">Скинути</a>

            </form>
        </div>

        <div class="movies-grid">
            <?php if (!empty($movies)): ?>
                <?php foreach ($movies as $movie): ?>
                    <a href="movie.php?id=<?= (int)$movie['id'] ?>" class="movie-card">
                        <div class="poster-wrapper">
                            <?php if ($movie['poster']): ?>
                                <img src="uploads/<?= e($movie['poster']) ?>"
                                     alt="<?= e($movie['title']) ?>">
                            <?php else: ?>
                                <img src="uploads/default.svg" alt="Немає постера">
                            <?php endif; ?>
                        </div>
                        <div class="movie-card-body">
                            <h2 class="movie-title">
                                <?= e($movie['title']) ?>
                                <?php if ($movie['is_favorite']): ?>
                                    <img src="icons/heart-red.svg" alt="Улюблене" class="icon-heart">
                                <?php endif; ?>
                            </h2>
                            <?php $slugs = $genresByMovie[$movie['id']] ?? []; ?>
                            <?php if (!empty($slugs)): ?>
                                <p class="movie-genres">
                                    <?= e(formatGenres($slugs, $allGenres)) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="movies-empty">За вашими фільтрами нічого не знайдено</p>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php
                    // Зберігаємо активні фільтри в посиланнях пагінації —
                    // щоб при переході на сторінку 2 жанр/сортування не скидались
                    $queryParams = array_merge($_GET, ['page' => $i]);
                    $link        = '?' . http_build_query($queryParams);
                    ?>
                    <a href="<?= e($link) ?>"
                       class="page-link <?= ($i === $currentPage) ? 'active' : '' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </main>

<?php require_once 'partials/footer.php'; ?>