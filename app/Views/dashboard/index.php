<?= $this->extend('layout/dashboard_template') ?>
<?= $this->section('dashboard_content') ?>
<?php
$user      = auth()->user();
$nama      = nama_tampil($user->full_name ?? '', $user->username);
$adaStatus = \App\Models\ArtikelModel::adaKolomStatus();
$jam       = (int) date('G');
$sapaan    = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));
?>

<div class="page-head">
    <div>
        <h1><?= esc($sapaan) ?>, <?= esc($nama) ?> 👋</h1>
        <p>Berikut ringkasan aktivitas tulisanmu.</p>
    </div>
    <a class="btn btn--primary" href="<?= base_url('dashboard/artikel/tambah') ?>">+ Tulis Artikel Baru</a>
</div>

<div class="stat-grid">
    <div class="stat">
        <span class="stat__icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
        <div><strong><?= number_format($total_artikel) ?></strong><span>Total artikel</span></div>
    </div>
    <div class="stat">
        <span class="stat__icon stat__icon--blue"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></span>
        <div><strong><?= number_format($total_views) ?></strong><span>Total dibaca</span></div>
    </div>
    <div class="stat">
        <span class="stat__icon stat__icon--green"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg></span>
        <div><strong><?= number_format($total_komentar) ?></strong><span>Komentar masuk</span></div>
    </div>
    <?php if ($adaStatus): ?>
        <div class="stat">
            <span class="stat__icon stat__icon--amber"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></span>
            <div><strong><?= number_format($total_draf) ?></strong><span>Draf tersimpan</span></div>
        </div>
    <?php endif ?>
</div>

<div class="section-head" style="display:flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
    <h2 style="margin: 0; font-size: 1.2rem;">Artikel terbaru kamu</h2>
    <a class="link-more" href="<?= base_url('dashboard/artikel') ?>">Kelola semua &rarr;</a>
</div>

<?php if (empty($artikel_terbaru)): ?>
    <div class="empty">
        <h3>Kamu belum menulis artikel</h3>
        <p>Mulai dari satu paragraf. Kamu bisa menyimpannya sebagai draf dulu.</p>
        <a class="btn btn--primary" href="<?= base_url('dashboard/artikel/tambah') ?>">Tulis artikel pertama</a>
    </div>
<?php else: ?>
    <div class="table-card">
        <div class="table-wrap">
            <table class="table">
                <tbody>
                    <?php foreach ($artikel_terbaru as $a): ?>
                        <?php $terbit = ! $adaStatus || ($a['status'] ?? 'published') === 'published'; ?>
                        <tr>
                            <td>
                                <a class="cell-title" href="<?= esc(base_url('artikel/' . $a['slug']), 'attr') ?>"><?= esc($a['judul']) ?></a>
                                <span class="cell-sub"><?= esc(tgl_indo($a['created_at'])) ?> &middot; <?= number_format($a['views']) ?> dibaca</span>
                            </td>
                            <?php if ($adaStatus): ?>
                                <td><span class="badge <?= $terbit ? 'badge--success' : 'badge--warning' ?>"><?= $terbit ? 'Terbit' : 'Draf' ?></span></td>
                            <?php endif ?>
                            <td class="actions"><a class="btn btn--ghost btn--sm" href="<?= base_url('dashboard/artikel/edit/' . (int) $a['id']) ?>">Edit</a></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
