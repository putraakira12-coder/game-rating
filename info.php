<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = in_array($_GET['hal'] ?? '', ['faq', 'privasi', 'ketentuan', 'bantuan'], true) ? (string)$_GET['hal'] : 'faq';
$pageTitle = match ($active) {
    'privasi' => 'Kebijakan Privasi',
    'ketentuan' => 'Syarat & Ketentuan',
    'bantuan' => 'Hubungi Kami',
    default => 'FAQ',
};

$faq = [
    ['Dari mana data peringkat game diambil?', 'Data diambil dari website resmi platform (Steam Charts, PlayStation Blog, Nintendo Store, Google Play, App Store, dan Xbox Wire) serta thread diskusi komunitas Reddit. Detail tautan tersedia di menu Sumber Data.'],
    ['Apakah harga dalam Rupiah harga resmi?', 'Harga merupakan konversi perkiraan ke Rupiah dari harga resmi dalam USD atau mata uang setempat. Harga akhir dapat berbeda sesuai kurs, pajak, dan promo toko.'],
    ['Apakah aplikasi ini menjual game?', 'Tidak. RatingTop hanya menyajikan database dan tautan ke toko resmi maupun marketplace. Transaksi dilakukan sepenuhnya di website tujuan.'],
    ['Bagaimana cara membaca skor rating?', 'Skor 9.0 ke atas sangat baik, 8.0–8.9 baik, 7.0–7.9 cukup baik. Skor merupakan ringkasan ulasan resmi dan sentimen komunitas, bukan nilai resmi penerbit.'],
    ['Bagaimana cara mengganti foto atau kata sandi admin?', 'Buka menu Profil Admin. Foto dapat diunggah langsung, sedangkan kata sandi dapat diganti dengan memasukkan kata sandi saat ini, kata sandi baru, dan konfirmasinya.'],
    ['Apakah tampilan website bisa diubah?', 'Ya. Tombol di bagian kanan atas mengubah tampilan antara mode gelap (default) dan mode terang. Sidebar juga dapat dibuka atau ditutup.'],
];

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2><?= e($pageTitle) ?></h2>
        <p>Informasi umum, bantuan, dan kebijakan penggunaan RatingTop.</p>
    </div>
    <div class="head-actions link-actions">
        <a class="btn btn-sm <?= $active === 'faq' ? 'btn-primary' : '' ?>" href="<?= BASE_URL ?>/info.php?hal=faq">FAQ</a>
        <a class="btn btn-sm <?= $active === 'privasi' ? 'btn-primary' : '' ?>" href="<?= BASE_URL ?>/info.php?hal=privasi">Privasi</a>
        <a class="btn btn-sm <?= $active === 'ketentuan' ? 'btn-primary' : '' ?>" href="<?= BASE_URL ?>/info.php?hal=ketentuan">Ketentuan</a>
        <a class="btn btn-sm <?= $active === 'bantuan' ? 'btn-primary' : '' ?>" href="<?= BASE_URL ?>/info.php?hal=bantuan">Kontak</a>
    </div>
</div>

<section class="card">
    <div class="card-body info-content" style="max-width:900px">
        <?php if ($active === 'faq'): ?>
            <h3>Pertanyaan yang Sering Diajukan</h3>
            <?php foreach ($faq as $item): ?>
                <details>
                    <summary><?= e($item[0]) ?></summary>
                    <p><?= e($item[1]) ?></p>
                </details>
            <?php endforeach; ?>

        <?php elseif ($active === 'privasi'): ?>
            <h3>Kebijakan Privasi</h3>
            <p>RatingTop menyimpan data akun administrator berupa nama, username, kata sandi dalam bentuk hash, nomor telepon, tautan media sosial, dan foto profil. Data tersebut hanya digunakan untuk kebutuhan autentikasi dan pengelolaan profil.</p>
            <p>Aplikasi tidak menjual atau membagikan data pribadi kepada pihak ketiga. Tautan eksternal yang tersedia mengarah ke situs resmi platform dan marketplace yang memiliki kebijakan privasi tersendiri.</p>
            <p>Admin dapat memperbarui atau menghapus data profil kapan saja melalui halaman Profil Admin.</p>

        <?php elseif ($active === 'ketentuan'): ?>
            <h3>Syarat &amp; Ketentuan</h3>
            <p>Data yang ditampilkan bersifat informatif dan dapat berubah sewaktu-waktu mengikuti perubahan chart resmi platform. Pengguna bertanggung jawab memverifikasi harga, ketersediaan, dan rating langsung pada situs tujuan sebelum melakukan pembelian.</p>
            <p>Nama game, logo, dan merek dagang merupakan hak milik penerbit atau pemiliknya masing-masing. RatingTop tidak berafiliasi secara resmi dengan Steam, Sony, Nintendo, Google, Apple, Xbox, atau marketplace yang tercantum.</p>

        <?php else: ?>
            <h3>Hubungi Kami</h3>
            <p>Untuk pertanyaan, koreksi data, atau kerja sama, silakan hubungi administrator melalui kontak yang tertera pada halaman Profil Admin.</p>
            <?php
            $kontak = admin_login();
            $items = [
                ['bi-telephone', 'Nomor Telepon', $kontak['no_telepon'] ?? ''],
                ['bi-envelope', 'Username', '@' . ($kontak['username'] ?? '')],
                ['bi-youtube', 'YouTube', $kontak['youtube'] ?? ''],
                ['bi-instagram', 'Instagram', $kontak['instagram'] ?? ''],
                ['bi-linkedin', 'LinkedIn', $kontak['linkedin'] ?? ''],
            ];
            foreach ($items as $item): ?>
                <div class="source-item">
                    <span class="source-icon"><i class="bi <?= $item[0] ?>"></i></span>
                    <div><strong><?= e($item[1]) ?></strong><small><?= e($item[2] ?: 'Belum diisi') ?></small></div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
