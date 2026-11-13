<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php
$hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$hariIni = $hari[(int) date('w')] . ', ' . date('j') . ' ' . $bulan[(int) date('n')] . ' ' . date('Y');

$unggulan = $terbaru[0] ?? null;
$sisa     = array_slice($terbaru, 1, 6);
?>

<section class="hero">
    <div class="hero__inner">
        <span class="hero__eyebrow"><?= esc($hariIni) ?></span>
        <h1>Tulisan, proyek, dan catatan belajar.</h1>
        <p class="hero__lead">Selamat datang di Ruang Rikza. Baca artikel terbaru, jelajahi proyek, atau mulai menulis ceritamu sendiri.</p>
        <div class="hero__actions">
            <a class="btn btn--light btn--lg" href="<?= base_url('artikel') ?>">Jelajahi Artikel</a>
            <a class="btn btn--ghost-light btn--lg" href="<?= base_url('projects') ?>">Lihat Proyek</a>
        </div>
        <div class="hero__stats">
            <div class="hero__stat"><strong><?= number_format($total_artikel) ?></strong><span>Artikel terbit</span></div>
            <div class="hero__stat"><strong><?= number_format(count($kategori)) ?></strong><span>Kategori</span></div>
        </div>
    </div>
</section>

<div class="container">
    <section class="section" aria-labelledby="judul-terbaru">
        <div class="section__head">
            <div>
                <h2 class="section__title" id="judul-terbaru">Artikel Terbaru</h2>
                <p class="section__sub">Tulisan paling baru dari para penulis.</p>
            </div>
            <a class="link-more" href="<?= base_url('artikel') ?>">Lihat semua &rarr;</a>
        </div>

        <?php if ($unggulan): ?>
            <?= view('blog/_unggulan', ['art' => $unggulan, 'label' => 'Terbaru']) ?>

            <?php if (! empty($sisa)): ?>
                <div class="grid">
                    <?php foreach ($sisa as $art): ?>
                        <?= view('blog/_kartu', ['art' => $art]) ?>
                    <?php endforeach ?>
                </div>
            <?php endif ?>
        <?php else: ?>
            <div class="empty">
                <div class="empty__icon"><svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                <h3>Belum ada artikel</h3>
                <p>Jadilah yang pertama menulis di sini.</p>
                <a class="btn btn--primary" href="<?= base_url(auth()->loggedIn() ? 'dashboard/artikel/tambah' : 'login') ?>"><?= auth()->loggedIn() ? 'Tulis artikel pertama' : 'Masuk untuk menulis' ?></a>
            </div>
        <?php endif ?>
    </section>

    <?php if (! empty($populer) || ! empty($kategori)): ?>
        <div class="section split">
            <?php if (! empty($populer)): ?>
                <section aria-labelledby="judul-populer">
                    <div class="section__head"><h2 class="section__title" id="judul-populer">Paling Banyak Dibaca</h2></div>
                    <div class="card card--pad mini-list">
                        <?php foreach ($populer as $i => $p): ?>
                            <a class="mini-item" href="<?= esc(base_url('artikel/' . $p['slug']), 'attr') ?>">
                                <span class="mini-item__rank"><?= $i + 1 ?></span>
                                <span>
                                    <span class="mini-item__title" style="display:block"><?= esc($p['judul']) ?></span>
                                    <span class="mini-item__meta"><?= number_format($p['views']) ?> views &middot; <?= esc(tgl_indo($p['created_at'])) ?></span>
                                </span>
                            </a>
                        <?php endforeach ?>
                    </div>
                </section>
            <?php endif ?>

            <?php if (! empty($kategori)): ?>
                <section aria-labelledby="judul-kategori">
                    <div class="section__head"><h2 class="section__title" id="judul-kategori">Jelajahi Kategori</h2></div>
                    <div class="cat-tiles" style="grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));">
                        <?php foreach ($kategori as $k): ?>
                            <a class="cat-tile" style="--tile: <?= safe_color($k['colour'] ?? null, '#29aafe') ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode($k['slug'])), 'attr') ?>">
                                <strong><?= esc($k['nama_kategori']) ?></strong>
                                <span><?= (int) $k['total_artikel'] ?> artikel</span>
                            </a>
                        <?php endforeach ?>
                    </div>
                </section>
            <?php endif ?>
        </div>
    <?php endif ?>

    <section class="section">
        <div class="cta-band">
            <div>
                <h3><?= auth()->loggedIn() ? 'Punya ide untuk dibagikan?' : 'Ingin ikut menulis?' ?></h3>
                <p><?= auth()->loggedIn() ? 'Tulis artikel baru, simpan sebagai draf, lalu terbitkan kapan pun kamu siap.' : 'Masuk atau daftar untuk menulis artikel, berkomentar, dan menyimpan bacaan favoritmu.' ?></p>
            </div>
            <a class="btn btn--light btn--lg" href="<?= base_url(auth()->loggedIn() ? 'dashboard/artikel/tambah' : 'login') ?>"><?= auth()->loggedIn() ? 'Tulis Artikel' : 'Masuk / Daftar' ?></a>
        </div>
    </section>
</div>
<?= $this->endSection() ?>
