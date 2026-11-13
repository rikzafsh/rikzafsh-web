<?php
/**
 * Artikel sebagai baris daftar (teks di kiri, gambar kecil di kanan).
 *
 * @var array $art Baris hasil ArtikelModel::kartu()
 */
$url    = base_url('artikel/' . $art['slug']);
$nama   = nama_tampil($art['full_name'] ?? '', $art['username'] ?? '');
$warna  = safe_color($art['kat_colour'] ?? null, '#703996');
$gambar = ! empty($art['thumbnail'])
    ? 'uploads/artikel/' . $art['thumbnail']
    : (! empty($art['cover_image']) ? 'uploads/kategori/' . $art['cover_image'] : null);
?>
<article class="post-row<?= $gambar ? '' : ' post-row--textonly' ?>">
    <div>
        <?php if (! empty($art['nama_kategori'])): ?>
            <a class="kicker" style="--kat: <?= $warna ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode((string) $art['kat_slug'])), 'attr') ?>"><?= esc($art['nama_kategori']) ?></a>
        <?php endif ?>
        <h3 class="post-row__title"><a href="<?= esc($url, 'attr') ?>"><?= esc($art['judul']) ?></a></h3>
        <p class="post-row__excerpt"><?= esc(ringkasan($art['konten'], 180)) ?></p>
        <div class="meta-line">
            <?php if (! empty($art['username'])): ?>
                <a href="<?= esc(base_url('profile/' . rawurlencode($art['username'])), 'attr') ?>"><?= esc($nama) ?></a>
            <?php endif ?>
            <span class="dot"><?= esc(tgl_indo($art['created_at'])) ?></span>
            <span class="dot"><?= waktu_baca($art['konten']) ?> mnt baca</span>
        </div>
    </div>
    <?php if ($gambar): ?>
        <a class="post-row__thumb" href="<?= esc($url, 'attr') ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= esc(base_url($gambar), 'attr') ?>" alt="" loading="lazy">
        </a>
    <?php endif ?>
</article>
