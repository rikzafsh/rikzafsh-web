<?php
/**
 * Kartu artikel untuk grid (daftar artikel, profil, artikel terkait).
 * Jika artikel tidak punya gambar, kartu tampil sebagai teks saja.
 *
 * @var array $art Baris hasil ArtikelModel::kartu()
 */
$url    = base_url('artikel/' . $art['slug']);
$nama   = nama_tampil($art['full_name'] ?? '', $art['username'] ?? '');
$warna  = safe_color($art['kat_colour'] ?? null, '#703996');
$gambar = ! empty($art['thumbnail'])
    ? 'uploads/artikel/' . $art['thumbnail']
    : (! empty($art['cover_image']) ? 'uploads/kategori/' . $art['cover_image'] : null);
$draf   = isset($art['status']) && $art['status'] !== 'published';
?>
<article class="post-card">
    <?php if ($gambar): ?>
        <a class="post-card__media" href="<?= esc($url, 'attr') ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= esc(base_url($gambar), 'attr') ?>" alt="" loading="lazy">
        </a>
    <?php endif ?>

    <div class="post-card__body">
        <div>
            <?php if (! empty($art['nama_kategori'])): ?>
                <a class="kicker" style="--kat: <?= $warna ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode((string) $art['kat_slug'])), 'attr') ?>"><?= esc($art['nama_kategori']) ?></a>
            <?php endif ?>
            <?php if ($draf): ?><span class="badge badge--warning">Draf</span><?php endif ?>
        </div>

        <h3 class="post-card__title"><a href="<?= esc($url, 'attr') ?>"><?= esc($art['judul']) ?></a></h3>
        <p class="post-card__excerpt"><?= esc(ringkasan($art['konten'], 160)) ?></p>

        <div class="meta-line">
            <?php if (! empty($art['username'])): ?>
                <a class="meta-author" href="<?= esc(base_url('profile/' . rawurlencode($art['username'])), 'attr') ?>">
                    <?= avatar_html($art['avatar'] ?? null, $nama, 'xs') ?> <?= esc($nama) ?>
                </a>
            <?php endif ?>
            <span class="dot" title="<?= esc(tgl_indo($art['created_at'], true), 'attr') ?>"><?= esc(tgl_indo($art['created_at'])) ?></span>
            <span class="dot"><?= waktu_baca($art['konten']) ?> mnt baca</span>
        </div>
    </div>
</article>
