<?php
/**
 * Artikel unggulan (satu artikel besar di atas daftar).
 * Tanpa gambar, tampil sebagai teks saja.
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
<article class="post-featured">
    <?php if ($gambar): ?>
        <a class="post-featured__media" href="<?= esc($url, 'attr') ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= esc(base_url($gambar), 'attr') ?>" alt="">
        </a>
    <?php endif ?>

    <?php if (! empty($art['nama_kategori'])): ?>
        <a class="kicker" style="--kat: <?= $warna ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode((string) $art['kat_slug'])), 'attr') ?>"><?= esc($art['nama_kategori']) ?></a>
    <?php endif ?>

    <h2 class="post-featured__title"><a href="<?= esc($url, 'attr') ?>"><?= esc($art['judul']) ?></a></h2>
    <p class="post-featured__excerpt"><?= esc(ringkasan($art['konten'], 240)) ?></p>

    <div class="meta-line">
        <?php if (! empty($art['username'])): ?>
            <a class="meta-author" href="<?= esc(base_url('profile/' . rawurlencode($art['username'])), 'attr') ?>">
                <?= avatar_html($art['avatar'] ?? null, $nama, 'xs') ?> <?= esc($nama) ?>
            </a>
        <?php endif ?>
        <span class="dot"><?= esc(tgl_indo($art['created_at'])) ?></span>
        <span class="dot"><?= waktu_baca($art['konten']) ?> mnt baca</span>
    </div>
</article>
