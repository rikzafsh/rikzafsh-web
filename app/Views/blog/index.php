<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php
$adaFilter = $keyword !== '' || $kategori_aktif !== '' || $penulis_aktif !== '';

// Membuat URL daftar artikel dengan mengubah sebagian parameter (halaman otomatis kembali ke 1)
$buatUrl = static function (array $ubah) use ($keyword, $kategori_aktif, $penulis_aktif, $sort_aktif): string {
    $q = [
        'cari'     => $keyword,
        'kategori' => $kategori_aktif,
        'penulis'  => $penulis_aktif,
        'sort'     => $sort_aktif !== 'terbaru' ? $sort_aktif : '',
    ];
    foreach ($ubah as $kunci => $nilai) {
        $q[$kunci] = $nilai;
    }
    $q = array_filter($q, static fn ($v) => $v !== '' && $v !== null);

    return base_url('artikel') . ($q ? '?' . http_build_query($q) : '');
};

if ($nama_kategori_aktif !== '') {
    $judulHalaman = 'Kategori: ' . $nama_kategori_aktif;
    $subJudul     = 'Kumpulan tulisan dalam kategori ini.';
} elseif ($nama_penulis_aktif !== '') {
    $judulHalaman = 'Tulisan oleh ' . $nama_penulis_aktif;
    $subJudul     = 'Semua artikel yang ditulis oleh penulis ini.';
} elseif ($keyword !== '') {
    $judulHalaman = 'Hasil pencarian';
    $subJudul     = 'Menampilkan artikel yang cocok dengan “' . $keyword . '”.';
} else {
    $judulHalaman = 'Semua Artikel';
    $subJudul     = 'Temukan tulisan, tutorial, dan catatan terbaru.';
}

$tampilkanUnggulan = ! $adaFilter && $sort_aktif === 'terbaru' && (int) $halaman_aktif === 1 && ! empty($daftar_artikel);
$unggulan          = $tampilkanUnggulan ? $daftar_artikel[0] : null;
$daftarGrid        = $tampilkanUnggulan ? array_slice($daftar_artikel, 1) : $daftar_artikel;
?>

<section class="page-hero">
    <div class="page-hero__inner">
        <h1><?= esc($judulHalaman) ?></h1>
        <p><?= esc($subJudul) ?></p>
    </div>
</section>

<div class="container pad-bottom">
    <form class="filter-bar" action="<?= base_url('artikel') ?>" method="get" role="search">
        <div class="filter-bar__row">
            <div class="search-field">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg>
                <label class="sr-only" for="cari">Kata kunci</label>
                <input class="input" type="search" id="cari" name="cari" value="<?= esc($keyword, 'attr') ?>" placeholder="Cari judul atau isi artikel…">
            </div>

            <label class="sr-only" for="sort">Urutkan</label>
            <select class="select" id="sort" name="sort" style="width: auto; min-width: 170px;" onchange="this.form.submit()">
                <option value="terbaru" <?= $sort_aktif === 'terbaru' ? 'selected' : '' ?>>Urutkan: Terbaru</option>
                <option value="populer" <?= $sort_aktif === 'populer' ? 'selected' : '' ?>>Urutkan: Terpopuler</option>
                <option value="terlama" <?= $sort_aktif === 'terlama' ? 'selected' : '' ?>>Urutkan: Terlama</option>
            </select>

            <?php if ($kategori_aktif !== ''): ?><input type="hidden" name="kategori" value="<?= esc($kategori_aktif, 'attr') ?>"><?php endif ?>
            <?php if ($penulis_aktif !== ''): ?><input type="hidden" name="penulis" value="<?= esc($penulis_aktif, 'attr') ?>"><?php endif ?>

            <button type="submit" class="btn btn--primary">Cari</button>
        </div>

        <?php if (! empty($semua_kategori)): ?>
            <div class="chips" role="group" aria-label="Filter kategori">
                <a class="chip<?= $kategori_aktif === '' ? ' is-active' : '' ?>" href="<?= esc($buatUrl(['kategori' => '']), 'attr') ?>">Semua</a>
                <?php foreach ($semua_kategori as $kat): ?>
                    <a class="chip<?= $kategori_aktif === $kat['slug'] ? ' is-active' : '' ?>" href="<?= esc($buatUrl(['kategori' => $kat['slug']]), 'attr') ?>">
                        <?= esc($kat['nama_kategori']) ?> <small><?= (int) $kat['total_artikel'] ?></small>
                    </a>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </form>

    <div class="result-info">
        <span>
            <?php if ($total_hasil > 0): ?>
                <strong><?= number_format($total_hasil) ?></strong> artikel ditemukan
            <?php else: ?>
                Tidak ada artikel yang cocok
            <?php endif ?>
        </span>

        <?php if ($adaFilter): ?>
            <span class="active-filters">
                <?php if ($keyword !== ''): ?>
                    <span class="tag-removable">Kata kunci: <?= esc($keyword) ?> <a href="<?= esc($buatUrl(['cari' => '']), 'attr') ?>" aria-label="Hapus filter kata kunci">&times;</a></span>
                <?php endif ?>
                <?php if ($nama_penulis_aktif !== ''): ?>
                    <span class="tag-removable">Penulis: <?= esc($nama_penulis_aktif) ?> <a href="<?= esc($buatUrl(['penulis' => '']), 'attr') ?>" aria-label="Hapus filter penulis">&times;</a></span>
                <?php endif ?>
                <a class="link-more" href="<?= base_url('artikel') ?>">Reset semua</a>
            </span>
        <?php endif ?>
    </div>

    <?php if (! empty($daftar_artikel)): ?>
        <?php if ($unggulan): ?>
            <?= view('blog/_unggulan', ['art' => $unggulan, 'label' => 'Artikel terbaru']) ?>
        <?php endif ?>

        <?php if (! empty($daftarGrid)): ?>
            <div class="grid">
                <?php foreach ($daftarGrid as $art): ?>
                    <?= view('blog/_kartu', ['art' => $art]) ?>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <?= $pager->links('default', 'ruang_rikza') ?>
    <?php else: ?>
        <div class="empty">
            <div class="empty__icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.3-4.3"/></svg></div>
            <h3>Artikel tidak ditemukan</h3>
            <p><?= $adaFilter ? 'Coba kata kunci lain atau hapus beberapa filter.' : 'Belum ada artikel yang diterbitkan.' ?></p>
            <?php if ($adaFilter): ?>
                <a class="btn btn--primary" href="<?= base_url('artikel') ?>">Tampilkan semua artikel</a>
            <?php elseif (auth()->loggedIn()): ?>
                <a class="btn btn--primary" href="<?= base_url('dashboard/artikel/tambah') ?>">Tulis artikel pertama</a>
            <?php endif ?>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
