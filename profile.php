<?php
require_once __DIR__ . '/includes/auth.php';
require_login();

$active = 'profile';
$pageTitle = 'Profil Admin';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_valid()) {
        set_flash('gagal', 'Token keamanan tidak valid. Silakan muat ulang halaman.');
        redirect('profile.php');
    }

    $aksi = (string)($_POST['aksi'] ?? '');

    if ($aksi === 'profil') {
        $nama = trim((string)($_POST['nama'] ?? ''));
        $username = strtolower(trim((string)($_POST['username'] ?? '')));
        $telepon = trim((string)($_POST['no_telepon'] ?? ''));
        $socials = [];
        foreach (['youtube', 'facebook', 'threads', 'instagram', 'linkedin', 'tiktok'] as $field) {
            $socials[$field] = trim((string)($_POST[$field] ?? ''));
        }
        $deskripsi = trim((string)($_POST['deskripsi'] ?? ''));

        if ($nama === '' || mb_strlen($nama) > 100) $errors[] = 'Nama wajib diisi dan maksimal 100 karakter.';
        if (!preg_match('/^[a-z0-9_.-]{3,50}$/', $username)) $errors[] = 'Username 3–50 karakter, hanya huruf kecil, angka, titik, garis bawah, dan tanda hubung.';

        $check = db()->prepare('SELECT id FROM admins WHERE username = :username AND id <> :id LIMIT 1');
        $check->execute(['username' => $username, 'id' => $_SESSION['admin_id']]);
        if ($check->fetch()) $errors[] = 'Username tersebut sudah digunakan admin lain.';

        $fotoName = null;
        if (!empty($_FILES['foto']['name']) && (int)$_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['foto'];
            $allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $info = @getimagesize($file['tmp_name']);
            if (!isset($allowed[$ext]) || !$info || $info['mime'] !== $allowed[$ext]) {
                $errors[] = 'Foto harus berformat JPG, PNG, WEBP, atau GIF.';
            } elseif ($file['size'] > 2 * 1024 * 1024) {
                $errors[] = 'Ukuran foto maksimal 2 MB.';
            } else {
                $fotoName = 'profil_' . (int)$_SESSION['admin_id'] . '_' . time() . '.' . $ext;
                if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . $fotoName)) {
                    $errors[] = 'Foto gagal diunggah. Periksa izin folder uploads/.';
                    $fotoName = null;
                }
            }
        }

        if (!$errors) {
            $oldStmt = db()->prepare('SELECT foto FROM admins WHERE id = :id');
            $oldStmt->execute(['id' => $_SESSION['admin_id']]);
            $fotoLama = (string)$oldStmt->fetchColumn();

            $sets = 'nama = :nama, username = :username, no_telepon = :telepon, youtube = :youtube,
                     facebook = :facebook, threads = :threads, instagram = :instagram, linkedin = :linkedin,
                     tiktok = :tiktok, deskripsi = :deskripsi';
            $params = [
                'nama' => $nama, 'username' => $username, 'telepon' => $telepon,
                'youtube' => $socials['youtube'], 'facebook' => $socials['facebook'],
                'threads' => $socials['threads'], 'instagram' => $socials['instagram'],
                'linkedin' => $socials['linkedin'], 'tiktok' => $socials['tiktok'],
                'deskripsi' => $deskripsi, 'id' => $_SESSION['admin_id'],
            ];
            if ($fotoName) {
                $sets .= ', foto = :foto';
                $params['foto'] = 'uploads/' . $fotoName;
            }
            $update = db()->prepare('UPDATE admins SET ' . $sets . ' WHERE id = :id');
            $update->execute($params);

            if ($fotoName && str_starts_with($fotoLama, 'uploads/') && is_file(UPLOAD_DIR . basename($fotoLama))) {
                unlink(UPLOAD_DIR . basename($fotoLama));
            }
            $_SESSION['admin_username'] = $username;
            set_flash('sukses', 'Profil admin berhasil diperbarui.');
            redirect('profile.php');
        }
    } elseif ($aksi === 'password') {
        $current = (string)($_POST['current_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');

        $stmt = db()->prepare('SELECT password FROM admins WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $_SESSION['admin_id']]);
        $hash = (string)$stmt->fetchColumn();

        if (!password_verify($current, $hash)) $errors[] = 'Kata sandi saat ini tidak benar.';
        if (mb_strlen($new) < 6) $errors[] = 'Kata sandi baru minimal 6 karakter.';
        if ($new !== $confirm) $errors[] = 'Konfirmasi kata sandi baru tidak cocok.';

        if (!$errors) {
            $update = db()->prepare('UPDATE admins SET password = :password WHERE id = :id');
            $update->execute(['password' => password_hash($new, PASSWORD_DEFAULT), 'id' => $_SESSION['admin_id']]);
            set_flash('sukses', 'Kata sandi berhasil diganti.');
            redirect('profile.php');
        }
    } else {
        $errors[] = 'Aksi tidak dikenal.';
    }

    foreach ($errors as $error) {
        set_flash('gagal', $error);
    }
    redirect('profile.php');
}

$adminData = admin_login();

include __DIR__ . '/includes/header.php';
?>

<div class="page-head">
    <div>
        <h2>Profil Admin</h2>
        <p>Kelola identitas, media sosial, foto profil, dan kata sandi akun administrator.</p>
    </div>
</div>

<div class="profile-head">
    <div class="profile-photo">
        <img id="fotoPreview" src="<?= e(BASE_URL . '/' . ltrim($adminData['foto'] ?? '', '/')) ?>" alt="Foto <?= e($adminData['nama']) ?>">
        <label for="foto" title="Ganti foto profil"><i class="bi bi-camera-fill"></i></label>
    </div>
    <div>
        <h3><?= e($adminData['nama']) ?></h3>
        <p>@<?= e($adminData['username']) ?> · <?= e($adminData['no_telepon'] ?: 'Nomor telepon belum diisi') ?></p>
        <span class="badge badge-ungu"><i class="bi bi-shield-check"></i> Administrator</span>
    </div>
</div>

<form method="post" enctype="multipart/form-data" action="<?= BASE_URL ?>/profile.php">
    <?= csrf_input() ?>
    <input type="hidden" name="aksi" value="profil">

    <div class="section-divider">Informasi Pribadi</div>
    <section class="card">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="nama">Nama Lengkap</label>
                    <input class="form-control" id="nama" name="nama" value="<?= e($adminData['nama']) ?>" required maxlength="100">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="username">Username</label>
                    <input class="form-control" id="username" name="username" value="<?= e($adminData['username']) ?>" required maxlength="50">
                    <div class="form-text">Digunakan untuk login. Huruf kecil dan angka.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="no_telepon">Nomor Telepon</label>
                    <input class="form-control" id="no_telepon" name="no_telepon" value="<?= e($adminData['no_telepon']) ?>" placeholder="08xxxxxxxxxx">
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="foto">Foto Profil</label>
                    <input class="form-control" id="foto" name="foto" type="file" accept="image/jpeg,image/png,image/webp,image/gif">
                    <div class="form-text">Opsional. JPG/PNG/WEBP/GIF, maksimal 2 MB. Klik ikon kamera pada foto untuk mengganti.</div>
                </div>
                <div class="col-12">
                    <label class="form-label" for="deskripsi">Deskripsi Admin</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Ceritakan peran Anda..."><?= e($adminData['deskripsi']) ?></textarea>
                </div>
            </div>
        </div>
    </section>

    <div class="section-divider">Media Sosial</div>
    <section class="card">
        <div class="card-body">
            <div class="row g-3">
                <?php
                $socialFields = [
                    'youtube' => 'bi-youtube', 'facebook' => 'bi-facebook', 'threads' => 'bi-threads',
                    'instagram' => 'bi-instagram', 'linkedin' => 'bi-linkedin', 'tiktok' => 'bi-tiktok',
                ];
                foreach ($socialFields as $field => $icon): ?>
                    <div class="col-md-6">
                        <label class="form-label" for="<?= $field ?>"><i class="bi <?= $icon ?>"></i> <?= e(ucfirst($field)) ?></label>
                        <input class="form-control" type="url" id="<?= $field ?>" name="<?= $field ?>" value="<?= e($adminData[$field]) ?>" placeholder="https://...">
                    </div>
                <?php endforeach; ?>
            </div>
            <div style="margin-top:18px">
                <button class="btn btn-primary" type="submit"><i class="bi bi-save"></i> Simpan Profil</button>
            </div>
        </div>
    </section>
</form>

<div class="section-divider">Keamanan Akun</div>
<section class="card">
    <form method="post" action="<?= BASE_URL ?>/profile.php">
        <?= csrf_input() ?>
        <input type="hidden" name="aksi" value="password">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="current_password">Kata Sandi Saat Ini</label>
                    <input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="new_password">Kata Sandi Baru</label>
                    <input class="form-control" type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="6" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="confirm_password">Ulangi Kata Sandi Baru</label>
                    <input class="form-control" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="6" required>
                </div>
            </div>
            <div style="margin-top:18px">
                <button class="btn btn-primary" type="submit"><i class="bi bi-key-fill"></i> Ganti Kata Sandi</button>
            </div>
        </div>
    </form>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
