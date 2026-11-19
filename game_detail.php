<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = 'game_detail';
$id = (int)($_GET['id'] ?? 0);

$stmt = db()->prepare(
    'SELECT g.*, p.nama AS platform, p.slug AS platform_slug, p.icon AS platform_icon, p.warna AS platform_warna,
            p.deskripsi AS platform_deskripsi, ge.nama AS genre
     FROM games g
     JOIN platforms p ON p.id = g.platform_id
     JOIN genres ge ON ge.id = g.genre_id
     WHERE g.id = :id LIMIT 1'
);
$stmt->execute(['id' => $id]);
$game = $stmt->fetch();

if (!$game) {
    set_flash('gagal', 'Game yang diminta tidak ditemukan.');
    redirect('games.php');
}

$pageTitle = $game['nama_game'];

$linkStmt = db()->prepare('SELECT * FROM game_links WHERE game_id = :id ORDER BY urutan ASC');
$linkStmt->execute(['id' => $id]);
$links = $linkStmt->fetchAll();

$ratingStmt = db()->prepare('SELECT * FROM ratings WHERE game_id = :id ORDER BY skor DESC');
$ratingStmt->execute(['id' => $id]);
$ratings = $ratingStmt->fetchAll();

$sourceStmt = db()->prepare('SELECT * FROM sources WHERE game_id = :id ORDER BY kategori, id');
$sourceStmt->execute(['id' => $id]);
$sources = $sourceStmt->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2>Detail Game</h2>
        <p><a href="<?= BASE_URL ?>/games.php">Data Game</a> / <?= e($game['nama_game']) ?></p>
    </div>
    <div class="head-actions">
        <a class="btn" href="<?= BASE_URL ?>/games.php"><i class="bi bi-arrow-left"></i> Kembali</a>
        <a class="btn btn-primary" href="<?= e(url_mulai($game['tautan_resmi'])) ?>" target="_blank" rel="noopener noreferrer"><i class="bi bi-box-arrow-up-right"></i> Situs Resmi</a>
    </div>
</div>

<div class="detail-hero">
    <img class="detail-cover" src="<?= BASE_URL ?>/<?= e($game['gambar']) ?>" alt="Cover <?= e($game['nama_game']) ?>">

    <div>
        <div class="link-actions" style="margin-bottom:8px">
            <span class="badge badge-<?= e($game['platform_slug']) ?>"><i class="bi <?= e($game['platform_icon']) ?>"></i><?= e($game['platform']) ?></span>
            <span class="badge badge-ungu"><i class="bi bi-tags"></i><?= e($game['genre']) ?></span>
            <span class="badge badge-abu"><i class="bi bi-person-badge"></i><?= e(teks_umur($game['umur'])) ?></span>
        </div>
        <h2 class="detail-title"><?= e($game['nama_game']) ?></h2>
        <p class="detail-desc"><?= e($game['deskripsi']) ?></p>
        <div class="detail-facts">
            <div class="fact"><small>Rating utama</small><strong><span class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$game['rating'], 1, '.', '') ?> / 10</span></strong></div>
            <div class="fact"><small>Harga (IDR)</small><strong><?= e(rupiah($game['harga_idr'])) ?></strong></div>
            <div class="fact"><small>Pengunduh</small><strong><?= e($game['label_pengunduh']) ?></strong></div>
            <div class="fact"><small>Platform</small><strong><?= e($game['platform']) ?></strong></div>
        </div>
        <p class="form-text" style="margin-top:12px"><i class="bi bi-info-circle"></i> <?= e($game['catatan_sumber']) ?></p>
    </div>
</div>

<div class="grid-2" style="margin-top:18px">
    <section class="card">
        <div class="card-head">
            <div><h3>Tautan Beli &amp; Toko</h3><p><?= count($links) ?> tautan resmi, toko digital, dan marketplace</p></div>
            <i class="bi bi-cart3" style="color:var(--cyan)"></i>
        </div>
        <div class="card-body">
            <div class="buy-grid">
                <?php foreach ($links as $link): ?>
                    <a class="buy-card" href="<?= e(url_mulai($link['url'])) ?>" target="_blank" rel="noopener noreferrer">
                        <i class="bi <?= $link['tipe'] === 'marketplace' ? 'bi-bag' : ($link['tipe'] === 'resmi' ? 'bi-patch-check' : 'bi-box-arrow-up-right') ?>"></i>
                        <span>
                            <strong><?= e($link['toko']) ?></strong>
                            <small><?= e(ucfirst($link['tipe'])) ?> · <?= e(rupiah($link['harga_idr'])) ?></small>
                        </span>
                        <i class="bi bi-arrow-up-right" style="font-size:13px"></i>
                    </a>
                <?php endforeach; ?>
            </div>
            <p class="form-text" style="margin-top:14px">Tautan marketplace mengarah ke halaman pencarian toko. Harga game di database menggunakan konversi perkiraan ke Rupiah.</p>
        </div>
    </section>

    <section class="card">
        <div class="card-head">
            <div><h3>Rincian Rating</h3><p>Skor dari situs resmi dan komunitas Reddit</p></div>
            <i class="bi bi-star-half" style="color:var(--amber)"></i>
        </div>
        <div class="card-body">
            <?php foreach ($ratings as $rating): ?>
                <div class="source-item">
                    <span class="source-icon"><i class="bi bi-star-fill"></i></span>
                    <div>
                        <strong><?= e($rating['sumber']) ?></strong>
                        <small><?= e($rating['catatan']) ?> · <?= angka($rating['votes']) ?> vote · <?= e(tanggal($rating['tanggal'])) ?></small>
                    </div>
                    <span class="rating-pill" style="margin-left:auto"><i class="bi bi-star-fill"></i><?= number_format((float)$rating['skor'], 1, '.', '') ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<section class="card" style="margin-top:18px">
    <div class="card-head">
        <div><h3>Sumber Data</h3><p>Website resmi platform dan diskusi Reddit yang digunakan</p></div>
        <a href="<?= BASE_URL ?>/sources.php">Semua sumber <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="card-body">
        <?php foreach ($sources as $source): ?>
            <div class="source-item">
                <span class="source-icon"><i class="bi <?= $source['kategori'] === 'reddit' ? 'bi-reddit' : 'bi-globe2' ?>"></i></span>
                <div>
                    <strong><?= e($source['judul']) ?></strong>
                    <small><?= $source['kategori'] === 'reddit' ? 'Reddit' : 'Situs resmi' ?> · <?= e(tanggal($source['tanggal'])) ?></small>
                </div>
                <a href="<?= e(url_mulai($source['url'])) ?>" target="_blank" rel="noopener noreferrer">Buka tautan <i class="bi bi-box-arrow-up-right"></i></a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
