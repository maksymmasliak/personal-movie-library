<?php

$config = parse_ini_file(__DIR__ . '/.env', false, INI_SCANNER_RAW);

if ($config === false) {
    error_log("db.php: неможливо прочитати .env файл.");
    http_response_code(500);
    die("Помилка сервісу: неможливо прочитати конфігурацію.");
}

$dsn = "mysql:host={$config['DB_HOST']};dbname={$config['DB_NAME']};charset={$config['DB_CHARSET']}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $config['DB_USER'], $config['DB_PASS'], $options);
} catch (\PDOException $e) {
    error_log("Помилка підключення до БД: " . $e->getMessage());
    http_response_code(500);
    die("Помилка сервісу: тимчасові проблеми з доступом до бази даних.");
}