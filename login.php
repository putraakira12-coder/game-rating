<?php
require_once __DIR__ . '/includes/auth.php';

if (sudah_login()) {
    redirect('dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        $error = 'Token keamanan tidak valid. Silakan coba lagi.';
    } else {
        $username = strtolower(trim((string)($_POST['username'] ?? '')));
        $password = (string)($_POST['password'] ?? '');
        if ($username === '' || $password === '') {
            $error = 'Username dan kata sandi wajib diisi.';
        } elseif (!coba_login($username, $password)) {
            $error = 'Username atau kata sandi salah.';
        } else {
            set_flash('sukses', 'Selamat datang kembali di RatingTop.');
            redirect('dashboard.php');
        }
    }
}

$unggulan = db()->query(
    'SELECT g.nama_game, g.gambar, g.rating, p.nama AS platform
     FROM games g JOIN platforms p ON p.id = g.platform_id
     ORDER BY g.rating DESC LIMIT 3'
)->fetchAll();
$flashMessages = ambil_flash();
?>
<!doctype html>
<html lang="id" data-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · <?= APP_NAME ?></title>
    <script>
        try {
            var savedTheme = localStorage.getItem('ratingtop-theme');
            if (savedTheme) document.documentElement.dataset.theme = savedTheme;
        } catch (err) {}
    </script>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap/bootstrap.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/fonts.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body class="login-page">
<div class="login-shell">
    <div class="login-showcase">
        <a class="brand" href="<?= BASE_URL ?>/login.php">
            <span class="brand-mark"><i class="bi bi-trophy-fill"></i></span>
            <span class="brand-text"><strong>Rating<span>Top</span></strong><small>Game Database</small></span>
        </a>
        <h2>Temukan game terbaik dari seluruh platform dalam satu database.</h2>
        <p>Peringkat, rating, jumlah unduhan, harga Rupiah, hingga tautan beli lengkap tersusun rapi di satu tempat.</p>
        <div class="login-feature"><i class="bi bi-controller"></i> 6 platform: Steam, PlayStation, Nintendo, Play Store, App Store, Xbox</div>
        <div class="login-feature"><i class="bi bi-star-fill"></i> Rating dari situs resmi dan komunitas Reddit</div>
        <div class="login-feature"><i class="bi bi-cart3"></i> Tautan ke toko resmi, Steam, Tokopedia, dan Shopee</div>

        <div class="login-mini">
            <?php foreach ($unggulan as $game): ?>
                <div class="login-mini-row">
                    <img src="<?= BASE_URL ?>/<?= e($game['gambar']) ?>" alt="">
                    <div><strong><?= e(potong($game['nama_game'], 26)) ?></strong><small><?= e($game['platform']) ?></small></div>
                    <span class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$game['rating'], 1, '.', '') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="login-form-side">
        <button type="button" class="icon-button theme-button" id="themeToggle" aria-label="Ganti tema" title="Ganti tema" style="position:absolute;right:18px;top:18px">
            <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
            <i class="bi bi-sun-fill theme-icon-light"></i>
        </button>
        <h2>Masuk ke Akun</h2>
        <p>Gunakan akun administrator untuk mengelola database RatingTop.</p>

        <?php if ($error): ?>
            <div class="flash flash-gagal" role="alert" style="margin-bottom:16px">
                <i class="bi bi-exclamation-octagon-fill"></i><span><?= e($error) ?></span>
            </div>
        <?php endif; ?>
        <?php foreach ($flashMessages as $flash): ?>
            <div class="flash flash-<?= e($flash['jenis']) ?>" role="alert" style="margin-bottom:16px">
                <i class="bi bi-info-circle-fill"></i><span><?= e($flash['pesan']) ?></span>
            </div>
        <?php endforeach; ?>

        <form method="post" action="<?= BASE_URL ?>/login.php">
            <?= csrf_input() ?>
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <div class="input-group-icon">
                    <i class="bi bi-person"></i>
                    <input class="form-control" type="text" id="username" name="username" placeholder="Masukkan username" autocomplete="username" required autofocus>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Kata Sandi</label>
                <div class="input-group-icon">
                    <i class="bi bi-lock"></i>
                    <input class="form-control" type="password" id="password" name="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required>
                </div>
            </div>
            <button class="btn btn-primary w-100" type="submit" style="min-height:46px"><i class="bi bi-box-arrow-in-right"></i> Masuk Sekarang</button>
        </form>

        <div class="login-hint">
            <strong>Akun demo:</strong> username <code>admin</code> · kata sandi <code>admin123</code><br>
            Kata sandi dapat diganti setelah masuk melalui menu Profil Admin.
        </div>
        <div class="login-foot">&copy; <?= date('Y') ?> <?= APP_NAME ?>. Seluruh hak dilindungi.</div>
    </div>
</div>
<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
