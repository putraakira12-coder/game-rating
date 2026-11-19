<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = 'dashboard';
$pageTitle = 'Dashboard';

$totalGame = (int)db()->query('SELECT COUNT(*) FROM games')->fetchColumn();
$totalPlatform = (int)db()->query('SELECT COUNT(*) FROM platforms')->fetchColumn();
$totalUnduhan = (int)db()->query('SELECT COALESCE(SUM(jumlah_pengunduh), 0) FROM games')->fetchColumn();
$rataRating = (float)db()->query('SELECT COALESCE(AVG(rating), 0) FROM games')->fetchColumn();

$topGames = db()->query(
    'SELECT g.id, g.nama_game, g.gambar, g.rating, g.jumlah_pengunduh, p.nama AS platform, p.slug AS platform_slug
     FROM games g JOIN platforms p ON p.id = g.platform_id
     ORDER BY g.rating DESC, g.jumlah_pengunduh DESC LIMIT 5'
)->fetchAll();

$platformStats = db()->query(
    'SELECT p.nama, p.slug, p.warna, COUNT(g.id) AS total
     FROM platforms p LEFT JOIN games g ON g.platform_id = p.id
     GROUP BY p.id ORDER BY p.urutan ASC'
)->fetchAll();

$recentGames = db()->query(
    'SELECT g.id, g.nama_game, g.gambar, g.rating, g.umur, g.label_pengunduh, g.harga_idr, g.jumlah_pengunduh, p.nama AS platform, p.slug AS platform_slug
     FROM games g JOIN platforms p ON p.id = g.platform_id
     ORDER BY g.id DESC LIMIT 5'
)->fetchAll();

$allRatings = db()->query('SELECT rating FROM games')->fetchAll(PDO::FETCH_COLUMN);
$buckets = ['9.0+' => 0, '8.0-8.9' => 0, '7.0-7.9' => 0, '< 7.0' => 0];
foreach ($allRatings as $value) {
    $value = (float)$value;
    if ($value >= 9) $buckets['9.0+']++;
    elseif ($value >= 8) $buckets['8.0-8.9']++;
    elseif ($value >= 7) $buckets['7.0-7.9']++;
    else $buckets['< 7.0']++;
}
$maxBucket = max(1, max($buckets));

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2>Selamat datang, <?= e(potong($admin['nama'] ?? 'Admin', 20)) ?></h2>
        <p>Ringkasan peringkat game terbaik dari seluruh platform. Data diperbarui 18 November 2026.</p>
    </div>
    <div class="head-actions">
        <a class="btn btn-primary" href="<?= BASE_URL ?>/games.php"><i class="bi bi-controller"></i> Kelola Data Game</a>
        <a class="btn" href="<?= BASE_URL ?>/sources.php"><i class="bi bi-link-45deg"></i> Sumber Data</a>
    </div>
</div>

<div class="stat-grid">
    <div class="stat-card" style="--stat-color: var(--accent)">
        <div class="stat-top"><span class="stat-icon"><i class="bi bi-controller"></i></span><span class="stat-trend">+<?= $totalGame ?> data</span></div>
        <div class="stat-value"><?= angka($totalGame) ?></div>
        <div class="stat-label">Total game terdaftar</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--cyan)">
        <div class="stat-top"><span class="stat-icon"><i class="bi bi-layers-fill"></i></span><span class="stat-trend">6 platform</span></div>
        <div class="stat-value"><?= angka($totalPlatform) ?></div>
        <div class="stat-label">Platform pencarian</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--amber)">
        <div class="stat-top"><span class="stat-icon"><i class="bi bi-star-fill"></i></span><span class="stat-trend">Top rating</span></div>
        <div class="stat-value"><?= number_format($rataRating, 1, '.', '') ?></div>
        <div class="stat-label">Rata-rata rating game</div>
    </div>
    <div class="stat-card" style="--stat-color: var(--green)">
        <div class="stat-top"><span class="stat-icon"><i class="bi bi-download"></i></span><span class="stat-trend">Agregat</span></div>
        <div class="stat-value"><?= $totalUnduhan >= 1000000000 ? number_format($totalUnduhan / 1000000000, 1, '.', '') . ' M' : angka($totalUnduhan) ?></div>
        <div class="stat-label">Total unduhan terdata</div>
    </div>
</div>

<div class="dashboard-grid" style="margin-top:18px">
    <section class="card">
        <div class="card-head">
            <div><h3>Top 5 Rating Game</h3><p>Diurutkan dari skor rating tertinggi</p></div>
            <a href="<?= BASE_URL ?>/ratings.php">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>
        <div class="rank-list">
            <?php foreach ($topGames as $index => $game): ?>
                <div class="rank-item">
                    <span class="rank-no"><?= $index + 1 ?></span>
                    <img src="<?= BASE_URL ?>/<?= e($game['gambar']) ?>" alt="<?= e($game['nama_game']) ?>">
                    <div class="rank-meta">
                        <strong><?= e($game['nama_game']) ?></strong>
                        <small><span class="badge badge-<?= e($game['platform_slug']) ?>"><?= e($game['platform']) ?></span></small>
                    </div>
                    <span class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$game['rating'], 1, '.', '') ?></span>
                    <a class="btn btn-sm btn-ghost" href="<?= BASE_URL ?>/game_detail.php?id=<?= (int)$game['id'] ?>" title="Lihat detail"><i class="bi bi-arrow-up-right"></i></a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="card">
        <div class="card-head">
            <div><h3>Distribusi Rating</h3><p>Sebaran skor seluruh game</p></div>
            <i class="bi bi-bar-chart-fill" style="color:var(--accent-light)"></i>
        </div>
        <div class="card-body">
            <?php foreach ($buckets as $label => $count): ?>
                <div class="bar-row">
                    <span><?= e($label) ?></span>
                    <div class="bar-track"><div class="bar-fill" style="width:<?= round($count / $maxBucket * 100) ?>%"></div></div>
                    <strong><?= $count ?></strong>
                </div>
            <?php endforeach; ?>

            <div class="section-divider">Game per platform</div>
            <?php foreach ($platformStats as $platform): ?>
                <div class="bar-row">
                    <span><?= e($platform['nama']) ?></span>
                    <div class="bar-track"><div class="bar-fill" style="width:<?= round($platform['total'] / max(1, $totalGame) * 100) ?>%; background:<?= e($platform['warna']) ?>"></div></div>
                    <strong><?= (int)$platform['total'] ?></strong>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<section class="card" style="margin-top:18px">
    <div class="card-head">
        <div><h3>Game Terbaru</h3><p>Entri yang baru ditambahkan ke database</p></div>
        <a href="<?= BASE_URL ?>/games.php">Lihat semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Game</th><th>Platform</th><th>Rating</th><th>Pengunduh</th><th>Harga</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($recentGames as $game): ?>
                    <tr>
                        <td>
                            <div class="game-cell">
                                <img src="<?= BASE_URL ?>/<?= e($game['gambar']) ?>" alt="">
                                <div><strong><?= e($game['nama_game']) ?></strong><small><?= e(teks_umur($game['umur'])) ?></small></div>
                            </div>
                        </td>
                        <td><span class="badge badge-<?= e($game['platform_slug']) ?>"><i class="bi <?= e(icon_platform($game['platform_slug'])) ?>"></i><?= e($game['platform']) ?></span></td>
                        <td><span class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$game['rating'], 1, '.', '') ?></span></td>
                        <td class="num"><?= e($game['jumlah_pengunduh'] >= 1000000000 ? '1 M+' : angka($game['jumlah_pengunduh'])) ?></td>
                        <td class="num"><?= e(rupiah($game['harga_idr'])) ?></td>
                        <td><a class="btn btn-sm" href="<?= BASE_URL ?>/game_detail.php?id=<?= (int)$game['id'] ?>">Detail</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
