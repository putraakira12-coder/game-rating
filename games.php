<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = 'games';
$pageTitle = 'Data Game';

$q = trim((string)($_GET['q'] ?? ''));
$platform = (string)($_GET['platform'] ?? '');
$genre = (string)($_GET['genre'] ?? '');
$sort = (string)($_GET['sort'] ?? 'rating');
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 10;

$sortMap = [
    'rating' => 'g.rating DESC, g.jumlah_pengunduh DESC',
    'nama' => 'g.nama_game ASC',
    'unduh' => 'g.jumlah_pengunduh DESC',
    'harga' => 'g.harga_idr ASC',
    'terbaru' => 'g.id DESC',
];
$orderBy = $sortMap[$sort] ?? $sortMap['rating'];

$where = [];
$params = [];
if ($q !== '') {
    $like = '%' . $q . '%';
    $pencarian = [
        'CAST(g.id AS CHAR) LIKE :q',
        'g.nama_game LIKE :q2',
        'g.slug LIKE :q3',
        'g.deskripsi LIKE :q4',
        'ge.nama LIKE :q5',
        'p.nama LIKE :q6',
        'p.slug LIKE :q7',
    ];
    foreach (['q', 'q2', 'q3', 'q4', 'q5', 'q6', 'q7'] as $key) {
        $params[$key] = $like;
    }
    if (ctype_digit($q)) {
        $pencarian[] = 'g.id = :qid';
        $params['qid'] = (int)$q;
    }
    $where[] = '(' . implode(' OR ', $pencarian) . ')';
}
if ($platform !== '') {
    $where[] = 'p.slug = :platform';
    $params['platform'] = $platform;
}
if ($genre !== '') {
    $where[] = 'g.genre_id = :genre';
    $params['genre'] = (int)$genre;
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$countStmt = db()->prepare(
    'SELECT COUNT(*) FROM games g JOIN platforms p ON p.id = g.platform_id JOIN genres ge ON ge.id = g.genre_id ' . $whereSql
);
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = db()->prepare(
    'SELECT g.*, p.nama AS platform, p.slug AS platform_slug, p.icon AS platform_icon, p.warna AS platform_warna, ge.nama AS genre
     FROM games g
     JOIN platforms p ON p.id = g.platform_id
     JOIN genres ge ON ge.id = g.genre_id
     ' . $whereSql . '
     ORDER BY ' . $orderBy . '
     LIMIT ' . $perPage . ' OFFSET ' . $offset
);
$stmt->execute($params);
$games = $stmt->fetchAll();

$linksByGame = [];
if ($games) {
    $ids = array_column($games, 'id');
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $linkStmt = db()->prepare(
        'SELECT game_id, toko, url, tipe FROM game_links WHERE game_id IN (' . $placeholders . ') ORDER BY urutan ASC'
    );
    $linkStmt->execute($ids);
    foreach ($linkStmt->fetchAll() as $link) {
        $linksByGame[$link['game_id']][] = $link;
    }
}

$platforms = db()->query('SELECT id, nama, slug FROM platforms ORDER BY urutan')->fetchAll();
$genres = db()->query('SELECT id, nama FROM genres ORDER BY nama')->fetchAll();

function query_tanpa(string $key): string
{
    $params = $_GET;
    unset($params[$key]);
    return http_build_query($params);
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2>Data Game</h2>
        <p>Kelola dan jelajahi <?= angka($total) ?> game dari seluruh platform. Cari berdasarkan ID, kategori/genre, atau platform. Klik detail untuk melihat rating dan tautan pembelian lengkap.</p>
    </div>
    <div class="head-actions">
        <a class="btn" href="<?= BASE_URL ?>/ratings.php"><i class="bi bi-star-fill"></i> Lihat Peringkat</a>
        <a class="btn btn-primary" href="<?= BASE_URL ?>/sources.php"><i class="bi bi-link-45deg"></i> Sumber Data</a>
    </div>
</div>

<section class="card">
    <form class="toolbar" method="get" action="<?= BASE_URL ?>/games.php">
        <div class="toolbar-search">
            <i class="bi bi-search"></i>
            <input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Cari ID, nama game, genre, atau platform...">
        </div>
        <select class="form-select" name="platform" aria-label="Filter platform">
            <option value="">Semua platform</option>
            <?php foreach ($platforms as $item): ?>
                <option value="<?= e($item['slug']) ?>" <?= $platform === $item['slug'] ? 'selected' : '' ?>><?= e($item['nama']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-select" name="genre" aria-label="Filter genre">
            <option value="">Semua genre</option>
            <?php foreach ($genres as $item): ?>
                <option value="<?= (int)$item['id'] ?>" <?= (string)$item['id'] === $genre ? 'selected' : '' ?>><?= e($item['nama']) ?></option>
            <?php endforeach; ?>
        </select>
        <select class="form-select" name="sort" aria-label="Urutkan">
            <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Rating tertinggi</option>
            <option value="nama" <?= $sort === 'nama' ? 'selected' : '' ?>>Nama A-Z</option>
            <option value="unduh" <?= $sort === 'unduh' ? 'selected' : '' ?>>Paling banyak diunduh</option>
            <option value="harga" <?= $sort === 'harga' ? 'selected' : '' ?>>Harga termurah</option>
            <option value="terbaru" <?= $sort === 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
        </select>
        <button class="btn btn-primary" type="submit"><i class="bi bi-funnel"></i> Filter</button>
        <?php if ($q !== '' || $platform !== '' || $genre !== ''): ?>
            <a class="btn btn-ghost" href="<?= BASE_URL ?>/games.php">Reset</a>
        <?php endif; ?>
        <span class="toolbar-info">Hal <?= $page ?> / <?= $totalPages ?></span>
    </form>

    <div class="table-wrap">
        <table class="data-table">
            <thead>
            <tr>
                <th style="width:42px">#</th>
                <th>Game</th>
                <th>Platform</th>
                <th>Genre</th>
                <th>Umur</th>
                <th>Rating</th>
                <th>Pengunduh</th>
                <th>Harga (IDR)</th>
                <th>Tautan Beli</th>
                <th>Aksi</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$games): ?>
                <tr><td colspan="10"><div class="empty-state"><i class="bi bi-search"></i><p>Tidak ada game yang cocok dengan filter ini.</p></div></td></tr>
            <?php endif; ?>
            <?php foreach ($games as $index => $game): ?>
                <?php
                $links = $linksByGame[$game['id']] ?? [];
                $displayLinks = array_slice($links, 0, 3);
                ?>
                <tr>
                    <td class="num"><?= $offset + $index + 1 ?></td>
                    <td>
                        <div class="game-cell">
                            <img src="<?= BASE_URL ?>/<?= e($game['gambar']) ?>" alt="Cover <?= e($game['nama_game']) ?>">
                            <div><strong><?= e($game['nama_game']) ?></strong><small><?= e(potong($game['deskripsi'], 48)) ?></small></div>
                        </div>
                    </td>
                    <td><span class="badge badge-<?= e($game['platform_slug']) ?>"><i class="bi <?= e($game['platform_icon']) ?>"></i><?= e($game['platform']) ?></span></td>
                    <td><?= e($game['genre']) ?></td>
                    <td><span class="badge badge-abu"><?= e(teks_umur($game['umur'])) ?></span></td>
                    <td><span class="badge badge-<?= e(badge_kelas((float)$game['rating'])) ?> rating-pill"><i class="bi bi-star-fill"></i><?= number_format((float)$game['rating'], 1, '.', '') ?></span></td>
                    <td class="num" title="<?= e($game['label_pengunduh']) ?>"><?= e($game['label_pengunduh']) ?></td>
                    <td class="num"><?= e(rupiah($game['harga_idr'])) ?></td>
                    <td>
                        <div class="link-actions">
                            <?php foreach ($displayLinks as $link): ?>
                                <a class="store-link" href="<?= e(url_mulai($link['url'])) ?>" target="_blank" rel="noopener noreferrer" title="Buka <?= e($link['toko']) ?>">
                                    <i class="bi <?= $link['tipe'] === 'marketplace' ? 'bi-bag' : 'bi-box-arrow-up-right' ?>"></i><?= e($link['toko']) ?>
                                </a>
                            <?php endforeach; ?>
                            <?php if (count($links) > 3): ?><span class="badge badge-abu">+<?= count($links) - 3 ?></span><?php endif; ?>
                        </div>
                    </td>
                    <td><a class="btn btn-sm btn-primary" href="<?= BASE_URL ?>/game_detail.php?id=<?= (int)$game['id'] ?>">Detail</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="pagination" aria-label="Halaman">
            <?php if ($page > 1): ?>
                <a href="?<?= e(query_tanpa('page')) ?>&page=<?= $page - 1 ?>"><i class="bi bi-chevron-left"></i></a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === 1 || $i === $totalPages || abs($i - $page) <= 1): ?>
                    <a class="<?= $i === $page ? 'active' : '' ?>" href="?<?= e(query_tanpa('page')) ?>&page=<?= $i ?>"><?= $i ?></a>
                <?php elseif (abs($i - $page) === 2): ?>
                    <span>…</span>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
                <a href="?<?= e(query_tanpa('page')) ?>&page=<?= $page + 1 ?>"><i class="bi bi-chevron-right"></i></a>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
