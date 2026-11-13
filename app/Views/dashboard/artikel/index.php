<?= $this->extend('layout/dashboard_template') ?>

<?= $this->section('dashboard_content') ?>
<?php
$isAdminOrDev = auth()->user()->inGroup('admin', 'developer');
$adaStatus    = \App\Models\ArtikelModel::adaKolomStatus();

// Tautan pengurutan: mempertahankan parameter lain di URL
$tautanUrut = static function (string $paramKolom, string $paramUrut, string $kolom, string $kolomAktif, string $urutAktif): string {
    $params = $_GET;
    unset($params['page'], $params['page_artikel_saya'], $params['page_semua_artikel'], $params['page_kategori']);
    $params[$paramKolom] = $kolom;
    $params[$paramUrut]  = ($kolomAktif === $kolom && $urutAktif === 'ASC') ? 'DESC' : 'ASC';

    return '?' . http_build_query($params);
};
$panah = static fn (string $kolom, string $kolomAktif, string $urutAktif): string
    => $kolomAktif === $kolom ? ($urutAktif === 'ASC' ? ' ▲' : ' ▼') : '';

$ikonMata     = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>';
$ikonKomentar = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';
$ikonSimpan   = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>';
?>

<div class="page-head">
    <div>
        <h1>Kelola Artikel</h1>
        <p>Tulis, edit, dan atur status artikelmu di satu tempat.</p>
    </div>
    <a class="btn btn--primary" href="<?= base_url('dashboard/artikel/tambah') ?>">+ Tulis Artikel Baru</a>
</div>

<?php if (! $adaStatus): ?>
    <div class="alert alert--info" role="status">
        Fitur draf/terbit belum aktif. Jalankan <code>php spark migrate</code> sekali untuk mengaktifkannya.
    </div>
<?php endif ?>

<?php if ($isAdminOrDev): ?>
    <div class="tabs" role="tablist">
        <button type="button" class="tab-btn" role="tab" data-tab="tab-saya">Artikel Saya <span class="badge badge--muted"><?= (int) $total_artikel_saya ?></span></button>
        <button type="button" class="tab-btn" role="tab" data-tab="tab-semua">Semua Artikel <span class="badge badge--muted"><?= (int) $total_semua_artikel ?></span></button>
        <button type="button" class="tab-btn" role="tab" data-tab="tab-kategori">Kategori <span class="badge badge--muted"><?= count($kategori) ?></span></button>
    </div>
<?php endif ?>

<!-- ============ ARTIKEL SAYA ============ -->
<div id="tab-saya" class="tab-panel is-active" role="tabpanel">
    <div class="table-card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th><a href="<?= esc($tautanUrut('sort_by', 'sort_order', 'judul', $sort_by, $sort_order), 'attr') ?>">Judul<?= $panah('judul', $sort_by, $sort_order) ?></a></th>
                        <?php if ($adaStatus): ?><th>Status</th><?php endif ?>
                        <th><a href="<?= esc($tautanUrut('sort_by', 'sort_order', 'kategori', $sort_by, $sort_order), 'attr') ?>">Kategori<?= $panah('kategori', $sort_by, $sort_order) ?></a></th>
                        <th class="num"><a href="<?= esc($tautanUrut('sort_by', 'sort_order', 'views', $sort_by, $sort_order), 'attr') ?>">Dibaca<?= $panah('views', $sort_by, $sort_order) ?></a></th>
                        <th class="num"><a href="<?= esc($tautanUrut('sort_by', 'sort_order', 'komentar', $sort_by, $sort_order), 'attr') ?>">Komentar<?= $panah('komentar', $sort_by, $sort_order) ?></a></th>
                        <th class="num"><a href="<?= esc($tautanUrut('sort_by', 'sort_order', 'simpan', $sort_by, $sort_order), 'attr') ?>">Disimpan<?= $panah('simpan', $sort_by, $sort_order) ?></a></th>
                        <th><a href="<?= esc($tautanUrut('sort_by', 'sort_order', 'created_at', $sort_by, $sort_order), 'attr') ?>">Dibuat<?= $panah('created_at', $sort_by, $sort_order) ?></a></th>
                        <th class="actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($artikel)): ?>
                        <tr><td colspan="<?= $adaStatus ? 8 : 7 ?>">
                            <div class="empty" style="border: 0; padding: 36px 10px;">
                                <h3>Belum ada artikel</h3>
                                <p>Mulai menulis artikel pertamamu sekarang.</p>
                                <a class="btn btn--primary" href="<?= base_url('dashboard/artikel/tambah') ?>">Tulis artikel</a>
                            </div>
                        </td></tr>
                    <?php else: ?>
                        <?php foreach ($artikel as $a): ?>
                            <?php $terbit = ! $adaStatus || ($a['status'] ?? 'published') === 'published'; ?>
                            <tr>
                                <td>
                                    <a class="cell-title" href="<?= esc(base_url('artikel/' . $a['slug']), 'attr') ?>"><?= esc($a['judul']) ?></a>
                                    <span class="cell-sub"><?= waktu_baca($a['konten']) ?> menit baca</span>
                                </td>
                                <?php if ($adaStatus): ?>
                                    <td><span class="badge <?= $terbit ? 'badge--success' : 'badge--warning' ?>"><?= $terbit ? 'Terbit' : 'Draf' ?></span></td>
                                <?php endif ?>
                                <td><span class="badge" style="background: <?= safe_color($a['colour'] ?? null, '#cbd5e1') ?>"><?= esc($a['nama_kategori'] ?? 'Umum') ?></span></td>
                                <td class="num"><span class="meta-item"><?= $ikonMata ?> <?= number_format($a['views']) ?></span></td>
                                <td class="num"><span class="meta-item"><?= $ikonKomentar ?> <?= number_format($a['total_komentar']) ?></span></td>
                                <td class="num"><span class="meta-item"><?= $ikonSimpan ?> <?= number_format($a['total_simpan']) ?></span></td>
                                <td><span title="<?= esc(tgl_indo($a['created_at'], true), 'attr') ?>"><?= esc(tgl_indo($a['created_at'])) ?></span></td>
                                <td class="actions">
                                    <a class="btn btn--ghost btn--sm" href="<?= base_url('dashboard/artikel/edit/' . (int) $a['id']) ?>">Edit</a>
                                    <?php if ($adaStatus): ?>
                                        <form action="<?= base_url('dashboard/artikel/status/' . (int) $a['id']) ?>" method="post">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn--ghost btn--sm"><?= $terbit ? 'Jadikan draf' : 'Terbitkan' ?></button>
                                        </form>
                                    <?php endif ?>
                                    <form action="<?= base_url('dashboard/artikel/hapus/' . (int) $a['id']) ?>" method="post" data-confirm="Hapus artikel ini? Tindakan ini tidak dapat dibatalkan.">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn--danger-ghost btn--sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <?= isset($pager) ? $pager->links('artikel_saya', 'ruang_rikza') : '' ?>
</div>

<?php if ($isAdminOrDev): ?>
<!-- ============ SEMUA ARTIKEL ============ -->
<div id="tab-semua" class="tab-panel" role="tabpanel">
    <div class="table-card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr>
                        <th><a href="<?= esc($tautanUrut('sort_semua', 'order_semua', 'judul', $sort_semua, $order_semua), 'attr') ?>">Judul<?= $panah('judul', $sort_semua, $order_semua) ?></a></th>
                        <th><a href="<?= esc($tautanUrut('sort_semua', 'order_semua', 'penulis', $sort_semua, $order_semua), 'attr') ?>">Penulis<?= $panah('penulis', $sort_semua, $order_semua) ?></a></th>
                        <?php if ($adaStatus): ?><th>Status</th><?php endif ?>
                        <th><a href="<?= esc($tautanUrut('sort_semua', 'order_semua', 'kategori', $sort_semua, $order_semua), 'attr') ?>">Kategori<?= $panah('kategori', $sort_semua, $order_semua) ?></a></th>
                        <th class="num"><a href="<?= esc($tautanUrut('sort_semua', 'order_semua', 'views', $sort_semua, $order_semua), 'attr') ?>">Dibaca<?= $panah('views', $sort_semua, $order_semua) ?></a></th>
                        <th><a href="<?= esc($tautanUrut('sort_semua', 'order_semua', 'created_at', $sort_semua, $order_semua), 'attr') ?>">Dibuat<?= $panah('created_at', $sort_semua, $order_semua) ?></a></th>
                        <th class="actions">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($semua_artikel)): ?>
                        <tr><td colspan="<?= $adaStatus ? 7 : 6 ?>"><div class="empty" style="border: 0; padding: 30px 10px;"><h3>Belum ada artikel</h3></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($semua_artikel as $sa): ?>
                            <?php $terbitSa = ! $adaStatus || ($sa['status'] ?? 'published') === 'published'; ?>
                            <tr>
                                <td><a class="cell-title" href="<?= esc(base_url('artikel/' . $sa['slug']), 'attr') ?>" target="_blank" rel="noopener"><?= esc($sa['judul']) ?></a></td>
                                <td>
                                    <a href="<?= esc(base_url('profile/' . rawurlencode($sa['username'] ?? '')), 'attr') ?>">
                                        <strong><?= esc(nama_tampil($sa['full_name'] ?? '', $sa['username'] ?? '')) ?></strong>
                                    </a>
                                    <span class="cell-sub" style="display:block;">@<?= esc($sa['username'] ?? '-') ?></span>
                                </td>
                                <?php if ($adaStatus): ?>
                                    <td><span class="badge <?= $terbitSa ? 'badge--success' : 'badge--warning' ?>"><?= $terbitSa ? 'Terbit' : 'Draf' ?></span></td>
                                <?php endif ?>
                                <td><span class="badge" style="background: <?= safe_color($sa['kat_colour'] ?? null, '#cbd5e1') ?>"><?= esc($sa['nama_kategori'] ?? 'Umum') ?></span></td>
                                <td class="num"><?= number_format($sa['views']) ?></td>
                                <td><?= esc(tgl_indo($sa['created_at'])) ?></td>
                                <td class="actions">
                                    <a class="btn btn--ghost btn--sm" href="<?= base_url('dashboard/artikel/edit/' . (int) $sa['id']) ?>">Edit</a>
                                    <form action="<?= base_url('dashboard/artikel/hapus/' . (int) $sa['id']) ?>" method="post" data-confirm="Hapus artikel ini? Tindakan ini tidak dapat dibatalkan.">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn--danger-ghost btn--sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <?= isset($pager) ? $pager->links('semua_artikel', 'ruang_rikza') : '' ?>
</div>

<!-- ============ KATEGORI ============ -->
<div id="tab-kategori" class="tab-panel" role="tabpanel">
    <div class="page-head" style="margin-bottom: 16px;">
        <p class="mb-0">Kategori membantu pembaca menemukan artikel.</p>
        <a class="btn btn--primary btn--sm" href="<?= base_url('dashboard/kategori/tambah') ?>">+ Kategori Baru</a>
    </div>
    <div class="table-card">
        <div class="table-wrap">
            <table class="table">
                <thead>
                    <tr><th>Kategori</th><th class="num">Artikel</th><th>Warna</th><th class="actions">Aksi</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($kategori)): ?>
                        <tr><td colspan="4"><div class="empty" style="border: 0; padding: 30px 10px;"><h3>Belum ada kategori</h3></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($kategori as $k): ?>
                            <?php $warnaK = safe_color($k['colour'] ?? null, '#cbd5e1'); ?>
                            <tr>
                                <td>
                                    <div class="flex items-center gap-8">
                                        <?php if (! empty($k['cover_image'])): ?>
                                            <img src="<?= esc(base_url('uploads/kategori/' . $k['cover_image']), 'attr') ?>" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover;">
                                        <?php else: ?>
                                            <span class="avatar avatar--md avatar--initial" style="background: <?= $warnaK ?>; border-radius: 6px;"><?= esc(mb_strtoupper(mb_substr($k['nama_kategori'], 0, 1, 'UTF-8'), 'UTF-8')) ?></span>
                                        <?php endif ?>
                                        <div>
                                            <a class="cell-title" href="<?= esc(base_url('artikel?kategori=' . rawurlencode((string) $k['slug'])), 'attr') ?>"><?= esc($k['nama_kategori']) ?></a>
                                            <span class="cell-sub"><?= esc($k['slug'] ?: 'tanpa slug') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="num"><?= (int) $k['total_artikel'] ?></td>
                                <td>
                                    <span class="flex items-center gap-8"><span style="width: 18px; height: 18px; border-radius: 50%; background: <?= $warnaK ?>; display: inline-block; border: 1px solid rgba(0,0,0,.1);"></span><code><?= esc(strtoupper($warnaK)) ?></code></span>
                                </td>
                                <td class="actions">
                                    <a class="btn btn--ghost btn--sm" href="<?= base_url('dashboard/kategori/edit/' . (int) $k['id']) ?>">Edit</a>
                                    <form action="<?= base_url('dashboard/kategori/hapus/' . (int) $k['id']) ?>" method="post" data-confirm="Hapus kategori ini? Artikel di dalamnya akan kehilangan kategori.">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn--danger-ghost btn--sm">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
    <?= isset($pager) ? $pager->links('kategori', 'ruang_rikza') : '' ?>
</div>
<?php endif ?>
<?= $this->endSection() ?>
