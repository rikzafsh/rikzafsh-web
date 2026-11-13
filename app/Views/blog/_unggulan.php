<?php
/**
 * Artikel unggulan (kartu besar).
 *
 * @var array  $art   Baris hasil ArtikelModel::kartu()
 * @var string $label Teks label kecil di atas judul
 */
$label  = $label ?? 'Terbaru';
$url    = base_url('artikel/' . $art['slug']);
$nama   = nama_tampil($art['full_name'] ?? '', $art['username'] ?? '');
$warna  = safe_color($art['kat_colour'] ?? null, '#29aafe');
$gambar = ! empty($art['thumbnail'])
    ? 'uploads/artikel/' . $art['thumbnail']
    : (! empty($art['cover_image']) ? 'uploads/kategori/' . $art['cover_image'] : null);
?>
<article class="post-featured">
    <a class="post-card__media" href="<?= esc($url, 'attr') ?>" tabindex="-1" aria-hidden="true">
        <?php if ($gambar): ?>
            <img src="<?= esc(base_url($gambar), 'attr') ?>" alt="">
        <?php else: ?>
            <div class="post-card__placeholder" style="--ph: <?= $warna ?>; font-size: 4rem;"><?= esc(mb_strtoupper(mb_substr($art['judul'], 0, 1, 'UTF-8'), 'UTF-8')) ?></div>
        <?php endif ?>
    </a>
    <div class="post-featured__body">
        <div class="flex items-center gap-8" style="flex-wrap: wrap;">
            <span class="post-featured__label"><?= esc($label) ?></span>
            <?php if (! empty($art['nama_kategori'])): ?>
                <a class="badge" style="background: <?= $warna ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode((string) $art['kat_slug'])), 'attr') ?>"><?= esc($art['nama_kategori']) ?></a>
            <?php endif ?>
        </div>
        <h2 class="post-featured__title"><a href="<?= esc($url, 'attr') ?>"><?= esc($art['judul']) ?></a></h2>
        <p class="post-featured__excerpt"><?= esc(ringkasan($art['konten'], 230)) ?></p>
        <div class="post-card__foot" style="border-top: 0; padding-top: 0;">
            <?php if (! empty($art['username'])): ?>
                <a class="post-card__author" href="<?= esc(base_url('profile/' . rawurlencode($art['username'])), 'attr') ?>">
                    <?= avatar_html($art['avatar'] ?? null, $nama, 'sm') ?>
                    <span><?= esc($nama) ?></span>
                </a>
            <?php else: ?>
                <span></span>
            <?php endif ?>
            <span class="post-card__meta">
                <span class="meta-item"><?= waktu_baca($art['konten']) ?> mnt baca</span>
                <span class="meta-item"><?= esc(tgl_indo($art['created_at'])) ?></span>
            </span>
        </div>
        <div><a class="btn btn--primary" href="<?= esc($url, 'attr') ?>">Baca selengkapnya &rarr;</a></div>
    </div>
</article>
