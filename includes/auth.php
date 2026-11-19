<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

function csrf_valid(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return is_string($token) && hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function set_flash(string $jenis, string $pesan): void
{
    $_SESSION['flash'][] = ['jenis' => $jenis, 'pesan' => $pesan];
}

function ambil_flash(): array
{
    $flash = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flash;
}

function coba_login(string $username, string $password): bool
{
    $stmt = db()->prepare('SELECT * FROM admins WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($password, $admin['password'])) {
        return false;
    }

    session_regenerate_id(true);
    $_SESSION['admin_id'] = (int)$admin['id'];
    $_SESSION['admin_username'] = $admin['username'];
    return true;
}

function sudah_login(): bool
{
    return !empty($_SESSION['admin_id']);
}

function require_login(): void
{
    if (!sudah_login()) {
        set_flash('peringatan', 'Silakan masuk terlebih dahulu untuk membuka halaman ini.');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function admin_login(): array
{
    static $admin = null;
    if ($admin !== null) {
        return $admin;
    }

    $stmt = db()->prepare('SELECT * FROM admins WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $_SESSION['admin_id'] ?? 0]);
    $admin = $stmt->fetch() ?: [];
    return $admin;
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . '/' . ltrim($path, '/'));
    exit;
}
