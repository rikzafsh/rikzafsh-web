<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

    <h1>Selamat Datang di Dasbor!</h1>
    
    <p>Halo, <strong><?= esc(auth()->user()->username ?? 'Pengguna') ?></strong>!</p>
    
    <p>Email kamu: <?= esc(auth()->user()->email ?? '-') ?></p>

    <hr>
    
    <a href="/logout">Logout (Keluar)</a>


<?= $this->endSection() ?>