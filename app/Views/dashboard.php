<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

    <h1>Selamat Datang di Dasbor!</h1>
    
    <p>Halo, <strong><?= auth()->user()->username ?? 'Pengguna' ?></strong>!</p>
    
    <p>Email kamu: <?= auth()->user()->email ?? '-' ?></p>

    <hr>
    
    <a href="/logout">Logout (Keluar)</a>


<?= $this->endSection() ?>