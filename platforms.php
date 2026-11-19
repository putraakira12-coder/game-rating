<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = 'platforms';
$pageTitle = 'Platform';

$platforms = db()->query(
    'SELECT p.*, COUNT(g.id) AS total_game, COALESCE(ROUND(AVG(g.rating), 1), 0) AS rata_rating
     FROM platforms p LEFT JOIN games g ON g.platform_id = p.id
     GROUP BY p.id ORDER BY p.urutan ASC'
)->fetchAll();

$topPerPlatform = db()->query(
    'SELECT p.id AS platform_id, g.id, g.nama_game, g.gambar, g.rating
     FROM games g JOIN platforms p ON p.id = g.platform_id
     WHERE g.rating = (SELECT MAX(g2.rating) FROM games g2 WHERE g2.platform_id = p.id)
     ORDER BY p.urutan ASC'
)->fetchAll();
$topByPlatform = [];
foreach ($topPerPlatform as $item) {
    if (!isset($topByPlatform[$item['platform_id']])) $topByPlatform[$item['platform_id']] = $item;
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2>Platform Game</h2>
        <p>Enam kategori platform sumber data: Steam, PlayStation, Nintendo, Play Store, App Store, dan Xbox.</p>
    </div>
    <div class="head-actions">
        <a class="btn btn-primary" href="<?= BASE_URL ?>/games.php"><i class="bi bi-controller"></i> Lihat Semua Game</a>
    </div>
</div>

<div class="grid-3">
    <?php foreach ($platforms as $platform): ?>
        <?php $top = $topByPlatform[$platform['id']] ?? null; ?>
        <article class="platform-card">
            <div class="platform-icon" style="--platform-color: <?= e($platform['warna']) ?>"><i class="bi <?= e($platform['icon']) ?>"></i></div>
            <h3><?= e($platform['nama']) ?></h3>
            <p><?= e($platform['deskripsi']) ?></p>
            <?php if ($top): ?>
                <div class="rank-item" style="padding:8px;margin-bottom:14px;background:var(--surface-2)">
                    <img src="<?= BASE_URL ?>/<?= e($top['gambar']) ?>" alt="">
                    <div class="rank-meta">
                        <strong><?= e($top['nama_game']) ?></strong>
                        <small>Game rating tertinggi</small>
                    </div>
                    <span class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$top['rating'], 1, '.', '') ?></span>
                </div>
            <?php endif; ?>
            <div class="platform-foot">
                <div><strong><?= (int)$platform['total_game'] ?></strong><small>game · rata-rata <?= number_format((float)$platform['rata_rating'], 1, '.', '') ?></small></div>
                <a href="<?= BASE_URL ?>/games.php?platform=<?= e($platform['slug']) ?>">Lihat game <i class="bi bi-arrow-right"></i></a>
            </div>
            <div style="margin-top:12px;border-top:1px solid var(--border-soft);padding-top:12px">
                <a href="<?= e(url_mulai($platform['situs_resmi'])) ?>" target="_blank" rel="noopener noreferrer" style="font-size:11px;color:var(--accent-light);font-weight:700">
                    <i class="bi bi-globe2"></i> Situs resmi platform <i class="bi bi-box-arrow-up-right"></i>
                </a>
            </div>
        </article>
    <?php endforeach; ?>
</div>

<section class="card" style="margin-top:18px">
    <div class="card-head"><div><h3>Ringkasan Platform</h3><p>Jumlah game dan kualitas rating tiap kategori</p></div><i class="bi bi-layers-fill" style="color:var(--accent-light)"></i></div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>#</th><th>Platform</th><th>Slug</th><th>Icon Bootstrap</th><th>Total Game</th><th>Rata-rating</th><th>Situs Resmi</th></tr></thead>
            <tbody>
                <?php foreach ($platforms as $index => $platform): ?>
                    <tr>
                        <td class="num"><?= $index + 1 ?></td>
                        <td><span class="badge badge-<?= e($platform['slug']) ?>"><i class="bi <?= e($platform['icon']) ?>"></i><?= e($platform['nama']) ?></span></td>
                        <td><code><?= e($platform['slug']) ?></code></td>
                        <td><code><?= e($platform['icon']) ?></code></td>
                        <td class="num"><?= (int)$platform['total_game'] ?></td>
                        <td><span class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$platform['rata_rating'], 1, '.', '') ?></span></td>
                        <td><a class="store-link" href="<?= e(url_mulai($platform['situs_resmi'])) ?>" target="_blank" rel="noopener noreferrer">Buka <i class="bi bi-box-arrow-up-right"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
