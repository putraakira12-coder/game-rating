        </main>

        <footer class="app-footer">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="<?= BASE_URL ?>/dashboard.php" class="brand brand-footer">
                        <span class="brand-mark"><i class="bi bi-trophy-fill"></i></span>
                        <span class="brand-text"><strong>Rating<span>Top</span></strong><small>Game Database</small></span>
                    </a>
                    <p>Database peringkat game terpopuler dari enam platform besar. Data rating, unduhan, dan harga dirangkum dari sumber resmi dan komunitas.</p>
                    <div class="footer-socials">
                        <?php
                        $socials = [
                            'youtube' => 'bi-youtube',
                            'facebook' => 'bi-facebook',
                            'threads' => 'bi-threads',
                            'instagram' => 'bi-instagram',
                            'linkedin' => 'bi-linkedin',
                            'tiktok' => 'bi-music-note-beamed',
                        ];
                        foreach ($socials as $field => $icon):
                            $url = trim((string)($admin[$field] ?? ''));
                            ?>
                            <a href="<?= $url !== '' ? e(url_mulai($url)) : '#' ?>"
                               class="footer-social" title="<?= e(ucfirst($field)) ?>"
                               <?= $url === '' ? 'aria-disabled="true"' : 'target="_blank" rel="noopener noreferrer"' ?>>
                                <i class="bi <?= $icon ?>"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="footer-col">
                    <h3>Jelajahi</h3>
                    <a href="<?= BASE_URL ?>/dashboard.php">Dashboard</a>
                    <a href="<?= BASE_URL ?>/games.php">Semua Game</a>
                    <a href="<?= BASE_URL ?>/ratings.php">Peringkat Rating</a>
                    <a href="<?= BASE_URL ?>/platforms.php">Platform</a>
                    <a href="<?= BASE_URL ?>/sources.php">Sumber Data</a>
                </div>

                <div class="footer-col">
                    <h3>Bantuan</h3>
                    <a href="<?= BASE_URL ?>/info.php?hal=faq">FAQ</a>
                    <a href="<?= BASE_URL ?>/info.php?hal=privasi">Kebijakan Privasi</a>
                    <a href="<?= BASE_URL ?>/info.php?hal=ketentuan">Syarat &amp; Ketentuan</a>
                    <a href="<?= BASE_URL ?>/info.php?hal=bantuan">Hubungi Kami</a>
                    <a href="<?= BASE_URL ?>/profile.php">Profil Admin</a>
                </div>

                <div class="footer-col">
                    <h3>Platform</h3>
                    <a href="<?= BASE_URL ?>/games.php?platform=steam">Steam</a>
                    <a href="<?= BASE_URL ?>/games.php?platform=playstation">PlayStation</a>
                    <a href="<?= BASE_URL ?>/games.php?platform=nintendo">Nintendo</a>
                    <a href="<?= BASE_URL ?>/games.php?platform=playstore">Play Store</a>
                    <a href="<?= BASE_URL ?>/games.php?platform=appstore">App Store</a>
                    <a href="<?= BASE_URL ?>/games.php?platform=xbox">Xbox</a>
                </div>
            </div>
            <div class="footer-bottom">
                <span>&copy; <?= date('Y') ?> <?= APP_NAME ?>. Hak cipta dilindungi.</span>
                <span>Dibuat dengan PHP Native · MySQL · Data: situs resmi &amp; Reddit</span>
            </div>
        </footer>
    </div>
</div>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
