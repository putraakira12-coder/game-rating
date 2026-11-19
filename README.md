# 🏆 RatingTop — Database Peringkat Game

Aplikasi web PHP berbasis XAMPP untuk mengelola dan menampilkan peringkat game dari berbagai platform: **Steam, PlayStation, Nintendo, Play Store, App Store, dan Xbox**. Data yang tersimpan meliputi rating, peringkat, genre, jumlah unduhan, harga dalam Rupiah, sumber rating, dan tautan pembelian.

---

## 👤 Informasi Pembuat

| Keterangan | Isi |
| --- | --- |
| **Nama lengkap** | **Akira Putra Pangestu** |
| **Kelas** | **XI PPLG** |
| GitHub | [@putraakira12-coder](https://github.com/putraakira12-coder) |
| Repository | [game-rating](https://github.com/putraakira12-coder/game-rating) |

---

## ✨ Fitur Utama

- 🔐 Login administrator dengan session, token **CSRF**, dan kata sandi terenkripsi (**bcrypt**).
- 📊 **Dashboard** berisi ringkasan/statistik database game.
- 🎮 **Data Game**: tambah, ubah, hapus, cari, filter, urutkan, dan pagination.
- 📄 **Detail game**: rating, genre, platform, harga, jumlah unduhan, dan tautan toko.
- ⭐ **Peringkat Rating**: urutan game berdasarkan nilai rating.
- 🧩 **Platform**: kelola data platform beserta deskripsi dan situs resminya.
- 🔗 **Sumber Data**: daftar sumber rating (situs resmi platform & komunitas).
- ❓ **FAQ** dan **Profil Admin** (foto profil, media sosial, dan ganti kata sandi).
- 🌗 Tema gelap/terang yang tersimpan di browser.

---

## 🗂️ Struktur Folder

```text
game-rating/
├── assets/                          # Aset statis (tidak perlu diubah)
│   ├── css/
│   │   ├── fonts.css                # Definisi font lokal
│   │   └── style.css                # Seluruh styling aplikasi
│   ├── fonts/
│   │   └── plus-jakarta-sans-*.woff2
│   ├── img/
│   │   ├── avatars/
│   │   │   └── admin-default.svg    # Foto profil bawaan
│   │   └── covers/                  # Cover/gambar game
│   ├── js/
│   │   └── app.js                   # Interaksi frontend (tema, sidebar, dll.)
│   └── vendor/                      # Bootstrap & Bootstrap Icons (offline)
│       ├── bootstrap/
│       └── bootstrap-icons/
├── database/
│   └── game_rating_top.sql          # Export database + data awal (seed)
├── includes/                        # Kode yang dipakai bersama
│   ├── auth.php                     # Login, session, CSRF, helper redirect
│   ├── config.php                   # Konfigurasi DB & BASE_URL
│   ├── db.php                       # Koneksi PDO ke MySQL
│   ├── footer.php                   # Layout footer
│   ├── header.php                   # Layout header
│   ├── helpers.php                  # Fungsi helper (e(), potong(), dll.)
│   └── sidebar.php                  # Menu navigasi
├── pages/                           # Folder cadangan untuk halaman tambahan
├── uploads/                         # Foto profil hasil unggahan admin
│   └── .gitkeep                     # Menjaga folder tetap ada di Git
├── dashboard.php                    # Halaman utama setelah login
├── games.php                        # Kelola data game
├── game_detail.php                  # Detail sebuah game
├── index.php                        # Redirect ke login.php / dashboard.php
├── info.php                         # Halaman FAQ/info
├── login.php                        # Halaman masuk
├── logout.php                       # Proses keluar
├── platforms.php                    # Kelola platform
├── profile.php                      # Profil & pengaturan admin
├── ratings.php                      # Peringkat rating game
├── sources.php                      # Sumber data rating
├── .gitignore                       # File yang diabaikan Git
└── README.md                        # Dokumentasi ini
```

---

## 🚀 Langkah-langkah Instalasi

### 1. Siapkan XAMPP
- Install [XAMPP](https://www.apachefriends.org/) (PHP 8.0+ dan MySQL/MariaDB).
- Jalankan **Apache** dan **MySQL** melalui XAMPP Control Panel.

### 2. Letakkan proyek
- Salin folder `game-rating` ke dalam `C:\xampp\htdocs\`.
- Hasil akhir: `C:\xampp\htdocs\game-rating\`.

### 3. Impor database
1. Buka `http://localhost/phpmyadmin`.
2. Klik tab **Import**.
3. Pilih file `database/game_rating_top.sql` (dari folder proyek).
4. Klik **Kirim / Go**.
5. Database `game_rating_top` beserta tabel dan data contoh akan dibuat otomatis.

### 4. Periksa konfigurasi
Buka `includes/config.php` dan sesuaikan jika diperlukan:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'game_rating_top');
define('DB_USER', 'root');
define('DB_PASS', '');              // XAMPP default: kosong
define('BASE_URL', '/game-rating'); // Sesuai nama folder di htdocs
```

> Jika nama folder di `htdocs` diubah, ubah juga nilai `BASE_URL`.

### 5. Pastikan folder `uploads/` dapat ditulis
Folder `uploads/` digunakan untuk menyimpan foto profil admin. Pastikan permission-nya tidak *read-only*.

### 6. Jalankan aplikasi
- Buka browser: **`http://localhost/game-rating/`**
- Anda akan diarahkan ke halaman `login.php`.

### 7. Masuk sebagai administrator

| Field | Nilai |
| --- | --- |
| Username | `admin` |
| Password | `admin123` |

> ⚠️ Kata sandi di atas adalah nilai bawaan dari file SQL. Segera ganti melalui menu **Profil Admin → Ubah Kata Sandi** setelah login pertama.

### 8. Mulai digunakan
Jelajahi menu: **Dashboard → Data Game → Peringkat Rating → Platform → Sumber Data → FAQ → Profil Admin**.

---

## 🛠️ Teknologi yang Digunakan

| Komponen | Keterangan |
| --- | --- |
| PHP | Backend (PDO, session, CSRF, password_hash) |
| MySQL / MariaDB | Database relasional |
| HTML5 & CSS3 | Tampilan responsif, tema gelap/terang |
| JavaScript | Interaksi frontend |
| Bootstrap 5 + Bootstrap Icons | UI (tersimpan lokal di `assets/vendor`) |
| XAMPP | Server lokal (Apache + MySQL + PHP) |
| Git & GitHub | Version control dan repository |

---

## 📝 Catatan

- Seluruh aset Bootstrap dan font disimpan lokal sehingga aplikasi tetap tampil normal tanpa internet.
- File SQL bersifat **destruktif**: mengimpor ulang akan menghapus tabel lama dan menggantinya dengan data awal. Backup database Anda sebelum mengimpor ulang.
- Dibuat untuk keperluan pembelajaran/pembuatan aplikasi berbasis PHP dan MySQL.

---

© 2026 **Akira Putra Pangestu** — Kelas **XI PPLG**.
