<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = 'ratings';
$pageTitle = 'Peringkat Rating';

$platformFilter = (string)($_GET['platform'] ?? '');
$params = [];
$filterSql = '';
if ($platformFilter !== '') {
    $filterSql = 'WHERE p.slug = :platform';
    $params['platform'] = $platformFilter;
}

$stmt = db()->prepare(
    'SELECT g.id, g.nama_game, g.gambar, g.rating, g.jumlah_pengunduh, g.label_pengunduh, g.harga_idr, g.umur,
            p.nama AS platform, p.slug AS platform_slug, ge.nama AS genre,
            (SELECT COUNT(*) FROM ratings r WHERE r.game_id = g.id) AS jumlah_sumber
     FROM games g
     JOIN platforms p ON p.id = g.platform_id
     JOIN genres ge ON ge.id = g.genre_id
     ' . $filterSql . '
     ORDER BY g.rating DESC, g.jumlah_pengunduh DESC'
);
$stmt->execute($params);
$ranked = $stmt->fetchAll();

$platforms = db()->query('SELECT id, nama, slug FROM platforms ORDER BY urutan')->fetchAll();

$ratingSumber = db()->query(
    'SELECT sumber, ROUND(AVG(skor), 1) AS rata, COUNT(*) AS total FROM ratings GROUP BY sumber ORDER BY total DESC'
)->fetchAll();

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2>Peringkat Rating</h2>
        <p>Urutan game terbaik berdasarkan skor rating dari situs resmi platform dan komunitas Reddit.</p>
    </div>
    <div class="head-actions">
        <form method="get" action="<?= BASE_URL ?>/ratings.php" class="link-actions">
            <select class="form-select" name="platform" onchange="this.form.submit()" aria-label="Filter platform">
                <option value="">Semua platform</option>
                <?php foreach ($platforms as $item): ?>
                    <option value="<?= e($item['slug']) ?>" <?= $platformFilter === $item['slug'] ? 'selected' : '' ?>><?= e($item['nama']) ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>
</div>

<?php if (count($ranked) >= 3): ?>
    <div class="grid-3">
        <?php foreach (array_slice($ranked, 0, 3) as $index => $game): ?>
            <?php $medals = ['bi-trophy-fill', 'bi-award-fill', 'bi-patch-check-fill']; ?>
            <div class="card platform-card" style="--platform-color: <?= $index === 0 ? 'var(--amber)' : ($index === 1 ? 'var(--muted)' : '#cd8a5a') ?>">
                <div class="rank-no" style="width:36px;height:36px;font-size:13px"><i class="bi <?= $medals[$index] ?>"></i></div>
                <img src="<?= BASE_URL ?>/<?= e($game['gambar']) ?>" alt="" style="width:100%;aspect-ratio:16/9;object-fit:cover;border-radius:12px;margin-top:12px">
                <h3><?= e($game['nama_game']) ?></h3>
                <p><?= e($game['platform']) ?> · <?= e($game['genre']) ?> · <?= e(teks_umur($game['umur'])) ?></p>
                <div class="platform-foot">
                    <div><strong><span class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$game['rating'], 1, '.', '') ?></span></strong><small>skor / 10</small></div>
                    <a href="<?= BASE_URL ?>/game_detail.php?id=<?= (int)$game['id'] ?>">Detail <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="grid-2" style="margin-top:18px">
    <section class="card">
        <div class="card-head"><div><h3>Leaderboard Lengkap</h3><p><?= count($ranked) ?> game dalam peringkat</p></div><i class="bi bi-list-ol" style="color:var(--accent-light)"></i></div>
        <div class="table-wrap">
            <table class="data-table">
                <thead><tr><th>#</th><th>Game</th><th>Platform</th><th>Rating</th><th>Sumber</th><th></th></tr></thead>
                <tbody>
                    <?php foreach ($ranked as $index => $game): ?>
                        <tr>
                            <td class="num"><strong style="color:var(--accent-light)"><?= $index + 1 ?></strong></td>
                            <td>
                                <div class="game-cell">
                                    <img src="<?= BASE_URL ?>/<?= e($game['gambar']) ?>" alt="">
                                    <div><strong><?= e($game['nama_game']) ?></strong><small><?= e($game['genre']) ?> · <?= e($game['label_pengunduh'] ?? '') ?></small></div>
                                </div>
                            </td>
                            <td><span class="badge badge-<?= e($game['platform_slug']) ?>"><?= e($game['platform']) ?></span></td>
                            <td>
                                <div class="rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$game['rating'], 1, '.', '') ?></div>
                                <div class="bar-track" style="width:80px;margin-top:4px"><div class="bar-fill" style="width:<?= (float)$game['rating'] * 10 ?>%"></div></div>
                            </td>
                            <td><span class="badge badge-abu"><?= (int)$game['jumlah_sumber'] ?> sumber</span></td>
                            <td><a class="btn btn-sm" href="<?= BASE_URL ?>/game_detail.php?id=<?= (int)$game['id'] ?>">Detail</a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <div class="card-head"><div><h3>Rata-rata per Sumber</h3><p>Ringkasan tabel ratings</p></div><i class="bi bi-diagram-3" style="color:var(--cyan)"></i></div>
        <div class="card-body">
            <?php foreach ($ratingSumber as $item): ?>
                <div class="source-item">
                    <span class="source-icon"><i class="bi bi-bar-chart-line"></i></span>
                    <div>
                        <strong><?= e($item['sumber']) ?></strong>
                        <small><?= (int)$item['total'] ?> penilaian</small>
                    </div>
                    <span class="rating-pill" style="margin-left:auto"><i class="bi bi-star-fill"></i><?= number_format((float)$item['rata'], 1, '.', '') ?></span>
                </div>
            <?php endforeach; ?>

            <div class="section-divider">Cara membaca skor</div>
            <div class="info-content">
                <p>Skor 9.0 ke atas menandakan game dengan penerimaan sangat positif. Skor 8.0–8.9 sangat baik, 7.0–7.9 baik, dan di bawah 7.0 perlu pertimbangan tambahan sebelum membeli.</p>
                <p>Nilai rating merupakan ringkasan yang dinormalisasi ke skala 10 dari ulasan toko resmi dan sentimen diskusi komunitas. Bukan nilai resmi dari penerbit game.</p>
            </div>
        </div>
    </section>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
