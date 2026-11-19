<?php
require_once __DIR__ . '/config.php';

function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo '<!doctype html><html><head><meta charset="utf-8"><title>Koneksi gagal</title></head>'
            . '<body style="font-family:Arial,sans-serif;background:#0b1020;color:#e8edf7;padding:40px">'
            . '<h1>Koneksi database gagal</h1><p>Pastikan MySQL/XAMPP sudah berjalan dan database <b>' . htmlspecialchars(DB_NAME)
            . '</b> sudah diimpor dari folder <b>database/</b>.</p></body></html>';
        exit;
    }

    return $pdo;
}
