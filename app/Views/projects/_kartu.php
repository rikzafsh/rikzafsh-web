<?php
/**
 * Kartu karya (satu edisi pustaka).
 *
 * @var array $mjl Baris tabel pustaka_karya
 */
$romawi  = edisi_romawi($mjl['edisi'] ?? 0);
$periode = periode_edisi($mjl['codename'] ?? '');
$warna   = safe_color($mjl['warna'] ?? null, '#3a43e0');
$teks    = warna_teks_kontras($warna);
$link    = safe_url($mjl['link'] ?? '');
$cover   = ! empty($mjl['cover_img']) && is_file(FCPATH . 'uploads/covers/' . $mjl['cover_img'])
    ? base_url('uploads/covers/' . $mjl['cover_img'])
    : null;
?>
<article class="proj-card">
    <div class="proj-card__cover" style="--warna: <?= $warna ?>; --teks: <?= $teks ?>">
        <?php if ($cover): ?>
            <img src="<?= esc($cover, 'attr') ?>" alt="Sampul edisi <?= esc($romawi, 'attr') ?>" loading="lazy">
        <?php else: ?>
            <div class="proj-card__fallback">
                <strong><?= esc($romawi) ?></strong>
                <span><?= esc($periode) ?></span>
            </div>
        <?php endif ?>
    </div>
    <div class="proj-card__body">
        <span class="kicker">Edisi <?= esc($romawi) ?> &middot; <?= esc($periode) ?></span>
        <h3 class="proj-card__title"><?= esc($mjl['tema'] ?? '') ?></h3>
        <span class="proj-card__meta">Rilis <?= esc(tgl_indo($mjl['tanggal_rilis'] ?? null)) ?></span>
        <?php if ($link !== ''): ?>
            <a class="proj-card__link" href="<?= esc($link, 'attr') ?>" target="_blank" rel="noopener noreferrer">Baca edisi &rarr;</a>
        <?php endif ?>
    </div>
</article>
