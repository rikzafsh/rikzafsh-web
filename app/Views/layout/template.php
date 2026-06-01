<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Rikza F. Sh.' ?></title>
    
    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">
</head>
<body data-theme="<?= $theme ?? 'normal' ?>">

    <header class="header-wrapper">
        <nav class="navbar">
            <div class="logo">
                <a href="/">Rikza F. Sh.</a>
            </div>
            
            <div class="navbar-right">
                <div class="profile-box">
                    <?php if (auth()->loggedIn()): ?>
                        <?php 
                            // Ambil data user langsung dari Shield agar bisa diakses di semua halaman tanpa lemparan controller
                            $currentUser = auth()->user(); 

                            // Cek apakah user punya file foto profil di database & filenya fisik ada di server
                            $avatarUrl = (!empty($currentUser->avatar) && file_exists(FCPATH . 'uploads/avatars/' . $currentUser->avatar)) 
                                ? base_url('uploads/avatars/' . $currentUser->avatar) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->username) . '&background=29aafe&color=fff&bold=true';
                        ?>
                        
                        <a href="<?= base_url('dashboard/profil') ?>" style="text-decoration: none; padding: 0px; display: inline-flex; align-items: center; gap: 10px; color: inherit;">
                            <img src="<?= $avatarUrl ?>" alt="Avatar" class="avatar" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover;">
                            
                            <span class="username" style="line-height: 1; font-weight: 600; color: #fff;">
                                <?= esc($currentUser->full_name ?? $currentUser->username) ?>
                            </span>
                        </a>
                    <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=Guest&background=cccccc&color=666" alt="Avatar" class="avatar" style="width: 35px; height: 35px; border-radius: 50%;">
                        <a href="<?= base_url('login') ?>" class="login-link">Masuk</a>
                    <?php endif; ?>
                </div>
                
                <button class="menu-btn" id="menuToggle">☰</button>
            </div>
        </nav>

        <div class="sub-navbar">
            <div class="menu-kiri">
                <a href="/">HOME</a>
                <a href="/artikel">ARTICLES</a>
                <a href="/project">PROJECTS</a>
            </div>
            <div class="tanggal-kanan">
                <?php
                    $namaBulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    echo date('d') . ' ' . $namaBulan[date('n')] . ' ' . date('Y');
                ?>
            </div>
        </div>
    </header>

    <div class="overlay" id="overlay"></div>

    <aside class="sidebar" id="sidebar">
        <button class="close-btn" id="closeSidebar">&times;</button>
        
        <h3 style="margin-top: 10px;">Menu Utama</h3>
        <a href="/">Beranda</a>
        
        <?php if (auth()->loggedIn()): ?>
            <a href="/dashboard">Dasbor Anggota</a>
            <a href="/dashboard/artikel/tambah">Tulis Artikel Baru</a>
        <?php endif; ?>

        <h3>Kategori Tulisan</h3>
        <a href="#"># Teknologi</a>
        <a href="#"># Tutorial</a>
        <a href="#"># Jurnal Pribadi</a>

        <?php if (auth()->loggedIn()): ?>
            <h3 style="margin-top: 40px;">Akun</h3>
            <a href="/logout" style="color: #d9534f; font-weight: bold;">Keluar (Logout)</a>
        <?php endif; ?>
    </aside>

    <div style="<?= url_is('dashboard*') ? 'width: 100%; margin: 0; padding: 0;' : 'max-width: 1200px; margin: 0 auto; padding: 20px;' ?>">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="footer">
        &copy; <?= date('Y') ?> Rikza F. Sh.
    </footer>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        function toggleSidebar() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        menuToggle.addEventListener('click', toggleSidebar);
        closeSidebar.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>