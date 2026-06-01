<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

    <h1 style="color: #703996;">Selamat Datang di Ruang Rikza!</h1>
    
    <p>Ini adalah halaman depan dari websitemu. Di sini nanti kita bisa menampilkan daftar artikel terbaru yang ditarik langsung dari database.</p>
    
    <div style="margin-top: 30px; padding: 20px; border-left: 4px solid #29aafe; background-color: #f9f9f9;">
        <h2>Eksperimen Pertama Berhasil</h2>
        <p>Template ini menggunakan sistem <strong>View Layouts</strong> dari CodeIgniter 4. Jika kamu ingin mengubah navbar, kamu cukup mengubahnya di satu file <code>template.php</code>, dan perubahannya akan terjadi di seluruh halaman!</p>
        <a href="#" class="btn">Mulai Membaca</a>
    </div>

<?= $this->endSection() ?>