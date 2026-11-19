<aside class="sidebar" id="sidebar">
    <a class="brand" href="<?= BASE_URL ?>/dashboard.php">
        <span class="brand-mark"><i class="bi bi-trophy-fill"></i></span>
        <span class="brand-text"><strong>Rating<span>Top</span></strong><small>Game Database</small></span>
    </a>

    <nav class="side-nav" aria-label="Navigasi utama">
        <p class="nav-label">Utama</p>
        <a href="<?= BASE_URL ?>/dashboard.php" class="nav-link <?= $active === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i><span>Dashboard</span>
        </a>
        <a href="<?= BASE_URL ?>/games.php" class="nav-link <?= in_array($active, ['games', 'game_detail'], true) ? 'active' : '' ?>">
            <i class="bi bi-controller"></i><span>Data Game</span>
        </a>
        <a href="<?= BASE_URL ?>/ratings.php" class="nav-link <?= $active === 'ratings' ? 'active' : '' ?>">
            <i class="bi bi-star-fill"></i><span>Peringkat Rating</span>
        </a>
        <a href="<?= BASE_URL ?>/platforms.php" class="nav-link <?= $active === 'platforms' ? 'active' : '' ?>">
            <i class="bi bi-layers-fill"></i><span>Platform</span>
        </a>

        <p class="nav-label">Referensi</p>
        <a href="<?= BASE_URL ?>/sources.php" class="nav-link <?= $active === 'sources' ? 'active' : '' ?>">
            <i class="bi bi-link-45deg"></i><span>Sumber Data</span>
            <small class="nav-count">2</small>
        </a>
        <a href="<?= BASE_URL ?>/info.php?hal=faq" class="nav-link <?= $active === 'faq' ? 'active' : '' ?>">
            <i class="bi bi-question-circle"></i><span>FAQ</span>
        </a>

        <p class="nav-label">Akun</p>
        <a href="<?= BASE_URL ?>/profile.php" class="nav-link <?= $active === 'profile' ? 'active' : '' ?>">
            <i class="bi bi-person-gear"></i><span>Profil Admin</span>
        </a>
        <a href="<?= BASE_URL ?>/logout.php" class="nav-link nav-logout">
            <i class="bi bi-box-arrow-right"></i><span>Keluar</span>
        </a>
    </nav>

    <div class="side-card">
        <div class="side-card-glow"></div>
        <i class="bi bi-lightning-charge-fill"></i>
        <strong>Data selalu diperbarui</strong>
        <p>Rating dan peringkat dirangkum dari situs resmi platform serta diskusi komunitas Reddit.</p>
        <a href="<?= BASE_URL ?>/sources.php">Lihat sumber <i class="bi bi-arrow-right"></i></a>
    </div>
</aside>
