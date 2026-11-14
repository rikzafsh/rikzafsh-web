<?php
helper(['security', 'blog']); // pastikan fungsi tampilan tersedia di semua halaman
$user        = auth()->loggedIn() ? auth()->user() : null;
$isStaff     = $user && $user->inGroup('admin', 'developer');
$namaUser    = $user ? nama_tampil($user->full_name ?? '', $user->username) : '';
$isDashboard = url_is('dashboard*');

$navKategori = [];
try {
    $navKategori = model(\App\Models\KategoriModel::class)->orderBy('nama_kategori', 'ASC')->findAll(6);
} catch (\Throwable $e) {
    $navKategori = [];
}

$aktif = static fn (string $pola): string => url_is($pola) ? ' is-active' : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Rikza F. Sh.') ?></title>
    <meta name="description" content="<?= esc($metaDescription ?? 'Ruang Rikza — tempat tulisan, proyek, dan catatan Rikza F. Sh.', 'attr') ?>">
    <meta name="theme-color" content="#703996">
    <?php if (! empty($ogImage)): ?>
        <meta property="og:image" content="<?= esc($ogImage, 'attr') ?>">
    <?php endif ?>
    <meta property="og:title" content="<?= esc($title ?? 'Rikza F. Sh.', 'attr') ?>">
    <meta property="og:type" content="<?= esc($ogType ?? 'website', 'attr') ?>">
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body data-theme="<?= esc($theme ?? 'normal') ?>">
    <a class="skip-link" href="#main">Lewati ke konten</a>
    <div class="read-progress" id="readProgress" aria-hidden="true"></div>

    <header class="site-header">
        <div class="container">
            <nav class="nav" aria-label="Navigasi utama">
                <a class="nav__brand" href="<?= base_url('/') ?>">
                    <span class="nav__logo" aria-hidden="true">R</span>
                    <span>Rikza F. Sh.</span>
                </a>

                <div class="nav__links" id="navLinks">
                    <a class="nav__link<?= $aktif('/') ?>" href="<?= base_url('/') ?>">Beranda</a>
                    <a class="nav__link<?= $aktif('artikel*') ?>" href="<?= base_url('artikel') ?>">Artikel</a>
                    <a class="nav__link<?= $aktif('projects*') ?>" href="<?= base_url('projects') ?>">Proyek</a>

                    <form class="nav__search nav__search-mobile" action="<?= base_url('artikel') ?>" method="get" role="search">
                        <input type="search" name="cari" placeholder="Cari artikel…" aria-label="Cari artikel">
                        <button type="submit" aria-label="Cari"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></button>
                    </form>
                </div>

                <div class="nav__spacer"></div>

                <div class="nav__actions">
                    <form class="nav__search" action="<?= base_url('artikel') ?>" method="get" role="search">
                        <input type="search" name="cari" placeholder="Cari artikel…" aria-label="Cari artikel" value="<?= esc(service('request')->getGet('cari') ?? '') ?>">
                        <button type="submit" aria-label="Cari"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></button>
                    </form>

                    <?php if ($user): ?>
                        <details class="user-menu">
                            <summary aria-label="Menu akun">
                                <?= avatar_html($user->avatar ?? null, $namaUser, 'sm') ?>
                                <span class="user-menu__name"><?= esc($namaUser) ?></span>
                            </summary>
                            <div class="user-menu__panel">
                                <div class="user-menu__head">
                                    <strong><?= esc($namaUser) ?></strong>
                                    <span><?= esc($user->email ?? '') ?></span>
                                </div>
                                <a class="user-menu__item" href="<?= base_url('dashboard') ?>">Dasbor</a>
                                <a class="user-menu__item" href="<?= base_url('dashboard/artikel/tambah') ?>">Tulis artikel baru</a>
                                <a class="user-menu__item" href="<?= base_url('dashboard/artikel') ?>">Kelola artikel</a>
                                <a class="user-menu__item" href="<?= base_url('dashboard/profil') ?>">Profil saya</a>
                                <a class="user-menu__item" href="<?= base_url('profile/' . rawurlencode($user->username)) ?>">Lihat profil publik</a>
                                <a class="user-menu__item user-menu__item--danger" href="<?= base_url('logout') ?>">Keluar</a>
                            </div>
                        </details>
                    <?php else: ?>
                        <a class="btn btn--light btn--sm" href="<?= base_url('login') ?>">Masuk</a>
                    <?php endif ?>

                    <button class="nav__toggle" id="navToggle" type="button" aria-label="Buka menu" aria-expanded="false" aria-controls="navLinks">☰</button>
                </div>
            </nav>
        </div>
    </header>

    <main id="main" class="main<?= $isDashboard ? ' main--full' : '' ?><?= ! empty($fullWidthPage) ? ' main--full' : '' ?>">
        <?php if ($isDashboard): ?>
            <?= $this->renderSection('content') ?>
        <?php elseif (! empty($fullWidthPage)): ?>
            <?php $flashHtml = trim($this->include('layout/flash')); ?>
            <?php if ($flashHtml !== ''): ?>
                <div class="container flash-wrap"><?= $flashHtml ?></div>
            <?php endif ?>
            <?= $this->renderSection('content') ?>
        <?php else: ?>
            <div class="container">
                <?= $this->include('layout/flash') ?>
                <?= $this->renderSection('content') ?>
            </div>
        <?php endif ?>
    </main>

    <footer class="footer">
        <div class="footer__grid">
            <div>
                <a class="footer__brand" href="<?= base_url('/') ?>"><span class="nav__logo" aria-hidden="true">R</span> Ruang Rikza</a>
                <p>Tempat menulis, berbagi proyek, dan mencatat hal-hal yang sedang dipelajari.</p>
            </div>
            <div>
                <h4>Jelajah</h4>
                <a href="<?= base_url('artikel') ?>">Semua artikel</a>
                <a href="<?= base_url('artikel?sort=populer') ?>">Artikel populer</a>
                <a href="<?= base_url('projects') ?>">Proyek</a>
                <?php if ($user): ?>
                    <a href="<?= base_url('dashboard/artikel/tambah') ?>">Tulis artikel</a>
                <?php else: ?>
                    <a href="<?= base_url('login') ?>">Masuk</a>
                <?php endif ?>
            </div>
            <div>
                <h4>Kategori</h4>
                <?php if (! empty($navKategori)): ?>
                    <?php foreach ($navKategori as $nk): ?>
                        <a href="<?= base_url('artikel?kategori=' . rawurlencode($nk['slug'])) ?>"><?= esc($nk['nama_kategori']) ?></a>
                    <?php endforeach ?>
                <?php else: ?>
                    <p>Belum ada kategori.</p>
                <?php endif ?>
            </div>
        </div>
        <div class="footer__bottom">&copy; <?= date('Y') ?> Rikza F. Sh. Semua hak dilindungi.</div>
    </footer>

    <script src="<?= base_url('js/app.js') ?>" defer></script>
</body>
</html>
