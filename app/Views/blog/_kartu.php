<?php
/**
 * Kartu artikel (dipakai di beranda, daftar artikel, profil, dan artikel terkait).
 *
 * @var array $art Baris hasil ArtikelModel::kartu()
 */
$url    = base_url('artikel/' . $art['slug']);
$nama   = nama_tampil($art['full_name'] ?? '', $art['username'] ?? '');
$warna  = safe_color($art['kat_colour'] ?? null, '#29aafe');
$gambar = ! empty($art['thumbnail'])
    ? 'uploads/artikel/' . $art['thumbnail']
    : (! empty($art['cover_image']) ? 'uploads/kategori/' . $art['cover_image'] : null);
$draf   = isset($art['status']) && $art['status'] !== 'published';
?>
<article class="post-card">
    <a class="post-card__media" href="<?= esc($url, 'attr') ?>" tabindex="-1" aria-hidden="true">
        <?php if ($gambar): ?>
            <img src="<?= esc(base_url($gambar), 'attr') ?>" alt="" loading="lazy">
        <?php else: ?>
            <div class="post-card__placeholder" style="--ph: <?= $warna ?>"><?= esc(mb_strtoupper(mb_substr($art['judul'], 0, 1, 'UTF-8'), 'UTF-8')) ?></div>
        <?php endif ?>
        <?php if (! empty($art['nama_kategori'])): ?>
            <span class="badge post-card__badge" style="background: <?= $warna ?>"><?= esc($art['nama_kategori']) ?></span>
        <?php endif ?>
    </a>

    <div class="post-card__body">
        <h3 class="post-card__title">
            <a href="<?= esc($url, 'attr') ?>"><?= esc($art['judul']) ?></a>
            <?php if ($draf): ?><span class="badge badge--warning">Draf</span><?php endif ?>
        </h3>
        <p class="post-card__excerpt"><?= esc(ringkasan($art['konten'], 160)) ?></p>

        <div class="post-card__foot">
            <?php if (! empty($art['username'])): ?>
                <a class="post-card__author" href="<?= esc(base_url('profile/' . rawurlencode($art['username'])), 'attr') ?>">
                    <?= avatar_html($art['avatar'] ?? null, $nama, 'xs') ?>
                    <span><?= esc($nama) ?></span>
                </a>
            <?php else: ?>
                <span></span>
            <?php endif ?>
            <span class="post-card__meta">
                <span class="meta-item" title="Perkiraan waktu baca"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg><?= waktu_baca($art['konten']) ?> mnt</span>
                <span class="meta-item" title="<?= esc(tgl_indo($art['created_at'], true), 'attr') ?>"><?= esc(tgl_indo($art['created_at'])) ?></span>
            </span>
        </div>
    </div>
</article>
