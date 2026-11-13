<?= $this->extend('layout/dashboard_template') ?>

<?= $this->section('dashboard_content') ?>
<?php
// Helper untuk Tab 1 (Artikel Saya)
$getSortLink = function($column) use ($sort_by, $sort_order) {
    $order = ($sort_by === $column && $sort_order === 'ASC') ? 'DESC' : 'ASC';
    $params = $_GET; $params['sort_by'] = $column; $params['sort_order'] = $order;
    return '?' . http_build_query($params);
};
$getSortIcon = function($column) use ($sort_by, $sort_order) {
    if ($sort_by !== $column) return '';
    return $sort_order === 'ASC' ? ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="vertical-align:middle;"><polyline points="18 15 12 9 6 15"></polyline></svg>' : ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="vertical-align:middle;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
};

$isAdminOrDev = auth()->user()->inGroup('admin', 'developer');

if ($isAdminOrDev) {
    // Helper untuk Tab 2 (Semua Artikel)
    $getSortLinkSemua = function($column) use ($sort_semua, $order_semua) {
        $order = ($sort_semua === $column && $order_semua === 'ASC') ? 'DESC' : 'ASC';
        $params = $_GET; $params['sort_semua'] = $column; $params['order_semua'] = $order;
        return '?' . http_build_query($params);
    };
    $getSortIconSemua = function($column) use ($sort_semua, $order_semua) {
        if ($sort_semua !== $column) return '';
        return $order_semua === 'ASC' ? ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="vertical-align:middle;"><polyline points="18 15 12 9 6 15"></polyline></svg>' : ' <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="vertical-align:middle;"><polyline points="6 9 12 15 18 9"></polyline></svg>';
    };
}
?>

<div style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); padding: 30px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <?php if (session()->getFlashdata('message')) : ?>
            <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;">
                <?= session()->getFlashdata('message'); ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')) : ?>
            <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;">
                <?= session()->getFlashdata('error'); ?>
            </div>
        <?php endif; ?>
        <h2 style="margin: 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">Kelola Artikel & Konten</h2>
        <a href="/dashboard/artikel/tambah" style="background: #009339; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">+ Tulis Artikel Baru</a>
    </div>

    <?php if ($isAdminOrDev): ?>
    <div style="display: flex; gap: 20px; border-bottom: 2px solid #edf2f7; margin-bottom: 25px; padding-bottom: 10px;">
        <button id="btn-tab-saya" onclick="switchTab('tab-saya', this)" class="tab-btn" style="background: none; border: none; font-size: 1rem; font-weight: 700; color: #4e73df; border-bottom: 3px solid #4e73df; padding-bottom: 8px; cursor: pointer;">Artikel Saya</button>
        <button id="btn-tab-semua" onclick="switchTab('tab-semua', this)" class="tab-btn" style="background: none; border: none; font-size: 1rem; font-weight: 600; color: #718096; padding-bottom: 8px; cursor: pointer;">Semua Artikel</button>
        <button id="btn-tab-kategori" onclick="switchTab('tab-kategori', this)" class="tab-btn" style="background: none; border: none; font-size: 1rem; font-weight: 600; color: #718096; padding-bottom: 8px; cursor: pointer;">Kelola Kategori</button>
    </div>
    <?php endif; ?>

    <div id="tab-saya" class="tab-content" style="display: block;">
        <div style="margin-bottom: 15px; color: #4a5568; font-weight: 600;">
            Total Artikel Anda: <span style="background: #edf2f7; padding: 2px 8px; border-radius: 4px; color: #2d3748;"><?= $total_artikel_saya ?></span>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 14px 12px;"><a href="<?= $getSortLink('judul') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Judul Artikel<?= $getSortIcon('judul') ?></a></th>
                        <th style="padding: 14px 12px;"><a href="<?= $getSortLink('kategori') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Kategori<?= $getSortIcon('kategori') ?></a></th>
                        <th style="padding: 14px 12px; text-align: center;"><a href="<?= $getSortLink('views') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Views<?= $getSortIcon('views') ?></a></th>
                        <th style="padding: 14px 12px; text-align: center;"><a href="<?= $getSortLink('komentar') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Komentar<?= $getSortIcon('komentar') ?></a></th>
                        <th style="padding: 14px 12px; text-align: center;"><a href="<?= $getSortLink('simpan') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Simpan<?= $getSortIcon('simpan') ?></a></th>
                        <th style="padding: 14px 12px;"><a href="<?= $getSortLink('created_at') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Tanggal & Jam Dibuat<?= $getSortIcon('created_at') ?></a></th>
                        <th style="padding: 14px 12px; text-align: center; color: #4a5568; font-weight: 700;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($artikel)): ?>
                        <tr><td colspan="7" style="padding: 40px; text-align: center; color: #a0aec0;">Belum ada artikel.</td></tr>
                    <?php else: ?>
                        <?php foreach ($artikel as $a): ?>
                        <tr style="border-bottom: 1px solid #edf2f7;">
                            <td style="padding: 16px 12px; max-width: 250px; line-height: 1.4;">
                                <a href="/artikel/<?= esc($a['slug']) ?>" style="text-decoration: none; color: #4a5568;">
                                <?= esc($a['judul']) ?>
                                </a>
                            </td>
                            <td style="padding: 16px 12px;">
                                <span style="color:<?= safe_color($a['colour'] ?? null, '#718096') ?>; background:<?= safe_color($a['colour'] ?? null, '#718096') ?>20; padding: 4px 10px; border-radius: 4px; font-size: 0.85rem; border: 1px solid <?= esc($a['colour'] ?? '#718096') ?>50; font-weight: 600;"><?= esc($a['nama_kategori'] ?? 'Umum') ?></span>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; color: #4a5568;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg> <?= number_format($a['views']) ?>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; color: #4a5568;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg> <?= number_format($a['total_komentar']) ?>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; color: #4a5568;">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle; margin-right:4px;"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"></path></svg> <?= number_format($a['total_simpan']) ?>
                            </td>
                            <td style="padding: 16px 12px; color: #718096; font-size: 0.9rem; white-space: nowrap;"><?= date('d M Y H:i', strtotime($a['created_at'])) ?></td>
                            <td style="padding: 16px 12px; text-align: center; white-space: nowrap;">
                                <a href="/dashboard/artikel/edit/<?= $a['id'] ?>" style="background: #ffc107; color: #000; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem; margin-right: 5px;">Edit</a>
                                <form action="/dashboard/artikel/hapus/<?= $a['id'] ?>" method="post" style="display: inline; margin: 0;" onsubmit="return confirm('Hapus artikel?')"><?= csrf_field() ?><button type="submit" style="background: #dc3545; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem; border: none; cursor: pointer; font-family: inherit;">Hapus</button></form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;"><?= isset($pager) ? $pager->links('artikel_saya', 'default_full') : '' ?></div>
    </div>

    <?php if ($isAdminOrDev): ?>
    <div id="tab-semua" class="tab-content" style="display: none;">
        <div style="margin-bottom: 15px; color: #4a5568; font-weight: 600;">
            Total Seluruh Artikel: <span style="background: #edf2f7; padding: 2px 8px; border-radius: 4px; color: #2d3748;"><?= $total_semua_artikel ?></span>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 14px 12px;"><a href="<?= $getSortLinkSemua('judul') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Judul Artikel<?= $getSortIconSemua('judul') ?></a></th>
                        <th style="padding: 14px 12px;"><a href="<?= $getSortLinkSemua('penulis') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Penulis<?= $getSortIconSemua('penulis') ?></a></th>
                        <th style="padding: 14px 12px;"><a href="<?= $getSortLinkSemua('kategori') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Kategori<?= $getSortIconSemua('kategori') ?></a></th>
                        <th style="padding: 14px 12px; text-align: center;"><a href="<?= $getSortLinkSemua('views') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Views<?= $getSortIconSemua('views') ?></a></th>
                        <th style="padding: 14px 12px;"><a href="<?= $getSortLinkSemua('created_at') ?>" style="color: #4a5568; text-decoration: none; font-weight: 700;">Tgl Dibuat<?= $getSortIconSemua('created_at') ?></a></th>
                        <th style="padding: 14px 12px; text-align: center; color: #4a5568; font-weight: 700;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($semua_artikel)): ?>
                        <tr><td colspan="6" style="padding: 40px; text-align: center; color: #a0aec0;">Belum ada artikel.</td></tr>
                    <?php else: ?>
                        <?php foreach ($semua_artikel as $sa): ?>
                        <tr style="border-bottom: 1px solid #edf2f7;">
                            <td style="padding: 16px 12px; max-width: 250px;"><a href="/artikel/<?= esc($sa['slug']) ?>" target="_blank" style="color: #2d3748; text-decoration: none; font-weight: 600; line-height: 1.4;"><?= esc($sa['judul']) ?></a></td>
                            <td style="padding: 16px 12px;"><a href="/profile/<?= esc($sa['username'] ?? '') ?>" style="text-decoration: none;"><div style="font-weight: 700; color: #2d3748;"><?= esc($sa['full_name'] ?? $sa['username']) ?></div><div style="font-size: 0.85rem; color: #718096;">@<?= esc($sa['username']) ?></div></a></td>
                            <td style="padding: 16px 12px;"><a href="/artikel?kategori=<?= esc($sa['kat_slug'] ?? '') ?>" style="text-decoration: none;"><span style="background-color: <?= esc($sa['kat_colour'] ?? '#718096') ?>18; color: <?= esc($sa['kat_colour'] ?? '#718096') ?>; padding: 5px 12px; border-radius: 4px; font-size: 0.8rem; font-weight: 700; border: 1px solid <?= esc($sa['kat_colour'] ?? '#718096') ?>30;"><?= esc($sa['nama_kategori'] ?? 'Umum') ?></span></a></td>
                            <td style="padding: 16px 12px; text-align: center; color: #4a5568;"><?= number_format($sa['views']) ?></td>
                            <td style="padding: 16px 12px; color: #718096; font-size: 0.9rem;"><?= date('d M Y', strtotime($sa['created_at'])) ?></td>
                            <td style="padding: 16px 12px; text-align: center;"><form action="/dashboard/artikel/hapus/<?= $sa['id'] ?>" method="post" style="display: inline; margin: 0;" onsubmit="return confirm('Hapus artikel?')"><?= csrf_field() ?><button type="submit" style="background: #dc3545; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem; border: none; cursor: pointer; font-family: inherit;">Hapus</button></form></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;"><?= isset($pager) ? $pager->links('semua_artikel', 'default_full') : '' ?></div>
    </div>

    <div id="tab-kategori" class="tab-content" style="display: none;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div style="color: #4a5568; font-weight: 600;">
                Total Kategori: <span style="background: #edf2f7; padding: 2px 8px; border-radius: 4px; color: #2d3748;"><?= count($kategori) ?></span>
            </div>
            <a href="/dashboard/kategori/tambah" style="background: #3a43e0; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">+ Kategori Baru</a>
        </div>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700;">Cover & Nama Kategori</th>
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700; text-align: center;">Jumlah Artikel</th>
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700;">Warna</th>
                        <th style="padding: 14px 12px; text-align: center; color: #4a5568; font-weight: 700;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kategori)): ?>
                        <tr><td colspan="4" style="padding: 40px; text-align: center; color: #a0aec0;">Belum ada kategori.</td></tr>
                    <?php else: ?>
                        <?php foreach ($kategori as $k): ?>
                        <tr style="border-bottom: 1px solid #edf2f7;">
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; gap: 15px; align-items: center;">
                                    <?php if (!empty($k['cover_image'])): ?>
                                        <img src="/uploads/kategori/<?= esc($k['cover_image']) ?>" alt="Cover" style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px; border: 1px solid #edf2f7;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 40px; background: #e2e8f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 0.75rem; font-weight: bold;">No Img</div>
                                    <?php endif; ?>
                                    
                                    <div>
                                        <a href="/artikel?kategori=<?= esc($k['slug']) ?>" style="color: #4e73df; text-decoration: none; font-weight: bold;"><?= esc($k['nama_kategori']) ?></a>
                                        <div style="font-size: 0.8rem; color: #a0aec0; margin-top: 2px; max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($k['slug'] ?? '') ?>">
                                            <?= esc($k['slug'] ?: 'Tidak ada slug.') ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; font-weight: bold; color: #2d3748;">
                                <?= $k['total_artikel'] ?>
                            </td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <?php $hexColor = safe_color($k['colour'] ?? null, '#cbd5e1'); ?>
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: <?= $hexColor ?>; border: 1px solid #cbd5e1;"></div>
                                    <span style="font-family: monospace; font-size: 0.85rem; color: #4a5568; background: #edf2f7; padding: 2px 6px; border-radius: 4px;"><?= strtoupper($hexColor) ?></span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; white-space: nowrap;">
                                <a href="/dashboard/kategori/edit/<?= $k['id'] ?>" style="display: inline-flex; background: #f59e0b; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem; margin-right: 6px;">Edit</a>
                                <form action="/dashboard/kategori/hapus/<?= $k['id'] ?>" method="post" style="display: inline; margin: 0;" onsubmit="return confirm('Hapus kategori ini?')"><?= csrf_field() ?><button type="submit" style="display: inline-flex; background: #ef4444; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem; border: none; cursor: pointer; font-family: inherit;">Hapus</button></form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top: 20px;"><?= isset($pager) ? $pager->links('kategori', 'default_full') : '' ?></div>
    </div>
    <?php endif; ?>
</div>

<script>
    // Jalankan pengecekan tab terakhir saat halaman dimuat
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil tab yang terakhir dibuka dari memori browser, default ke 'tab-saya'
        let activeTab = localStorage.getItem('activeDashboardTab') || 'tab-saya';
        let activeBtn = document.getElementById('btn-' + activeTab);
        
        // Jika tombolnya ada, trigger fungsi switchTab agar tampilannya menyesuaikan
        if (activeBtn) {
            switchTab(activeTab, activeBtn);
        }
    });

    // Ini adalah script yang bertugas memindahkan tab
    function switchTab(tabId, element) {
        // 1. Sembunyikan semua konten tab
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.style.display = 'none';
        });
        
        // 2. Tampilkan konten tab yang diklik
        document.getElementById(tabId).style.display = 'block';

        // 3. Reset warna dan gaya semua tombol tab
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.style.color = '#718096';
            btn.style.borderBottom = 'none';
            btn.style.fontWeight = '600';
        });

        // 4. Berikan highlight pada tombol tab yang sedang aktif
        element.style.color = '#4e73df';
        element.style.borderBottom = '3px solid #4e73df';
        element.style.fontWeight = '700';

        // 5. Simpan state tab yang aktif ke LocalStorage agar diingat browser
        localStorage.setItem('activeDashboardTab', tabId);
    }
</script>

<?= $this->endSection() ?>