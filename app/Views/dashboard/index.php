<?= $this->extend('layout/dashboard_template') ?>
<?= $this->section('dashboard_content') ?>
<div class="dashboard-container" style="padding: 20px;">
    <h2>Beranda Dashboard</h2>
    <p>Selamat datang kembali, <strong><?= auth()->user()->username ?></strong>!</p>

    <div style="display: flex; gap: 20px; margin-top: 20px;">
        <div style="background: #f4f6f9; padding: 20px; border-radius: 8px; flex: 1; border: 1px solid #ddd;">
            <h3 style="margin: 0; color: #555;">Total Artikel Saya</h3>
            <p style="font-size: 2em; font-weight: bold; margin: 10px 0 0 0;"><?= $total_artikel ?></p>
        </div>
        <div style="background: #f4f6f9; padding: 20px; border-radius: 8px; flex: 1; border: 1px solid #ddd;">
            <h3 style="margin: 0; color: #555;">Total Views</h3>
            <p style="font-size: 2em; font-weight: bold; margin: 10px 0 0 0;"><?= number_format($total_views) ?></p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>