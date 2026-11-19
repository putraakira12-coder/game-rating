<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = 'sources';
$pageTitle = 'Sumber Data';

$kategori = (string)($_GET['kategori'] ?? '');
$params = [];
$filterSql = '';
if (in_array($kategori, ['situs_resmi', 'reddit'], true)) {
    $filterSql = 'WHERE s.kategori = :kategori';
    $params['kategori'] = $kategori;
}

$stmt = db()->prepare(
    'SELECT s.*, g.nama_game, g.gambar, g.rating, p.nama AS platform, p.slug AS platform_slug
     FROM sources s JOIN games g ON g.id = s.game_id JOIN platforms p ON p.id = g.platform_id
     ' . $filterSql . '
     ORDER BY s.id DESC'
);
$stmt->execute($params);
$sources = $stmt->fetchAll();

$rekap = db()->query('SELECT kategori, COUNT(*) AS total FROM sources GROUP BY kategori')->fetchAll();
$jumlahResmi = 0;
$jumlahReddit = 0;
foreach ($rekap as $item) {
    if ($item['kategori'] === 'situs_resmi') $jumlahResmi = (int)$item['total'];
    if ($item['kategori'] === 'reddit') $jumlahReddit = (int)$item['total'];
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2>Sumber Data</h2>
        <p>Semua peringkat diambil dari website resmi platform dan diskusi komunitas Reddit.</p>
    </div>
    <div class="head-actions">
        <a class="btn <?= $kategori === '' ? 'btn-primary' : '' ?>" href="<?= BASE_URL ?>/sources.php">Semua (<?= $jumlahResmi + $jumlahReddit ?>)</a>
        <a class="btn <?= $kategori === 'situs_resmi' ? 'btn-primary' : '' ?>" href="<?= BASE_URL ?>/sources.php?kategori=situs_resmi">Situs Resmi (<?= $jumlahResmi ?>)</a>
        <a class="btn <?= $kategori === 'reddit' ? 'btn-primary' : '' ?>" href="<?= BASE_URL ?>/sources.php?kategori=reddit">Reddit (<?= $jumlahReddit ?>)</a>
    </div>
</div>

<div class="grid-2">
    <section class="card">
        <div class="card-head">
            <div><h3>Website Resmi</h3><p>Steam Charts, PlayStation Blog, Nintendo Store, Google Play, App Store, Xbox Wire</p></div>
            <i class="bi bi-globe2" style="color:var(--cyan)"></i>
        </div>
        <div class="card-body info-content">
            <p>Data peringkat penjualan unduhan diambil langsung dari halaman chart resmi masing-masing platform. Data harga dikonversi ke Rupiah menggunakan perkiraan kurs.</p>
            <ul>
                <li><b>Steam:</b> Top Sellers dan Most Played charts</li>
                <li><b>PlayStation:</b> PS Store Monthly Top Downloads</li>
                <li><b>Nintendo:</b> Best-selling games dan siaran pers resmi</li>
                <li><b>Google Play / App Store:</b> Top charts dan halaman detail aplikasi</li>
                <li><b>Xbox:</b> Xbox Wire dan pengumuman Game Pass</li>
            </ul>
        </div>
    </section>

    <section class="card">
        <div class="card-head">
            <div><h3>Reddit</h3><p>Sentimen komunitas sebagai pelengkap skor rating</p></div>
            <i class="bi bi-reddit" style="color:var(--amber)"></i>
        </div>
        <div class="card-body info-content">
            <p>Thread komunitas seperti r/gaming, r/pcgaming, dan r/Games digunakan sebagai referensi diskusi, bukan sebagai angka penjualan resmi.</p>
            <p>Skor dari Reddit dinormalisasi ke skala 10 dan digabungkan dengan ulasan toko resmi agar menghasilkan rating akhir yang ditampilkan di aplikasi.</p>
            <p class="form-text">Semua tautan eksternal terbuka di tab baru.</p>
        </div>
    </section>
</div>

<section class="card" style="margin-top:18px">
    <div class="card-head"><div><h3>Daftar Referensi</h3><p><?= count($sources) ?> sumber ditampilkan</p></div><i class="bi bi-link-45deg" style="color:var(--accent-light)"></i></div>
    <div class="table-wrap">
        <table class="data-table">
            <thead><tr><th>Game</th><th>Platform</th><th>Jenis</th><th>Judul Sumber</th><th>Tanggal</th><th>Tautan</th></tr></thead>
            <tbody>
                <?php foreach ($sources as $source): ?>
                    <tr>
                        <td>
                            <div class="game-cell">
                                <img src="<?= BASE_URL ?>/<?= e($source['gambar']) ?>" alt="">
                                <div><strong><?= e($source['nama_game']) ?></strong><small>rating <?= number_format((float)$source['rating'], 1, '.', '') ?></small></div>
                            </div>
                        </td>
                        <td><span class="badge badge-<?= e($source['platform_slug']) ?>"><?= e($source['platform']) ?></span></td>
                        <td>
                            <span class="badge <?= $source['kategori'] === 'reddit' ? 'badge-merah' : 'badge-hijau' ?>">
                                <i class="bi <?= $source['kategori'] === 'reddit' ? 'bi-reddit' : 'bi-globe2' ?>"></i>
                                <?= $source['kategori'] === 'reddit' ? 'Reddit' : 'Resmi' ?>
                            </span>
                        </td>
                        <td style="max-width:340px"><?= e($source['judul']) ?></td>
                        <td class="num"><?= e(tanggal($source['tanggal'])) ?></td>
                        <td><a class="store-link" href="<?= e(url_mulai($source['url'])) ?>" target="_blank" rel="noopener noreferrer">Buka <i class="bi bi-box-arrow-up-right"></i></a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
