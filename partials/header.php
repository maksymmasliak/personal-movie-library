<!doctype html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $pageTitle ?? 'Кінорецензія' ?></title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-content">
        <a href="index.php" class="logo-text">Кінорецензія</a>
        <nav>
            <?php if ($currentScript !== 'trash.php'): ?>
                <a href="trash.php" class="btn-action btn-trash-link">Кошик</a>
            <?php endif; ?>

            <?php if ($currentScript !== 'create.php'): ?>
                <a href="create.php" class="btn-add">Додати фільм</a>
            <?php endif; ?>
        </nav>
    </div>
</header>