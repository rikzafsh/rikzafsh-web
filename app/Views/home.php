<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<section class="hero">
    <div class="hero__grid">
        <div>
            <span class="eyebrow">Ruang Rikza</span>
            <h1>Karya, tulisan, dan proyek dalam satu ruang.</h1>
            <p class="hero__lead">Kumpulan publikasi, artikel, dan proyek digital Rikza F. Sh., disusun agar mudah dijelajahi dan dibaca.</p>
            <div class="hero__actions">
                <a class="btn btn--primary btn--lg" href="<?= base_url('projects') ?>">Lihat Karya</a>
                <a class="btn btn--light btn--lg" href="<?= base_url('artikel') ?>">Baca Artikel</a>
            </div>
        </div>

        <aside class="glance" aria-label="Ringkasan situs">
            <h2 class="glance__title">Sekilas</h2>
            <div class="glance__row"><span>Edisi karya</span><strong><?= number_format($total_karya) ?></strong></div>
            <div class="glance__row"><span>Artikel terbit</span><strong><?= number_format($total_artikel) ?></strong></div>
            <div class="glance__row"><span>Topik tulisan</span><strong><?= number_format(count($kategori)) ?></strong></div>
            <div class="glance__row"><span>Total dibaca</span><strong><?= number_format($total_views) ?></strong></div>
        </aside>
    </div>
</section>

<?php if (! empty($karya)): ?>
    <section class="band band--white" aria-labelledby="judul-karya">
        <div class="container">
            <div class="sec-head">
                <div>
                    <span class="eyebrow">Publikasi</span>
                    <h2 id="judul-karya">Pustaka Karya</h2>
                    <p>Edisi terbaru dari publikasi yang dikelola di Ruang Rikza.</p>
                </div>
                <a class="link-more" href="<?= base_url('projects') ?>">Lihat semua karya &rarr;</a>
            </div>
            <div class="proj-grid">
                <?php foreach ($karya as $mjl): ?>
                    <?= view('projects/_kartu', ['mjl' => $mjl]) ?>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?php endif ?>

<?php if (! empty($kategori)): ?>
    <section class="band" aria-labelledby="judul-topik">
        <div class="container">
            <div class="sec-head">
                <div>
                    <span class="eyebrow">Topik</span>
                    <h2 id="judul-topik">Bidang Tulisan</h2>
                    <p>Jelajahi artikel berdasarkan topik yang dibahas.</p>
                </div>
            </div>
            <div class="topic-grid">
                <?php foreach ($kategori as $k): ?>
                    <a class="topic-card" style="--kat: <?= safe_color($k['colour'] ?? null, '#29aafe') ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode($k['slug'])), 'attr') ?>">
                        <h3><?= esc($k['nama_kategori']) ?></h3>
                        <p><?= ! empty($k['deskripsi']) ? esc(mb_strimwidth($k['deskripsi'], 0, 130, '…', 'UTF-8')) : 'Kumpulan artikel dengan topik ' . esc($k['nama_kategori']) . '.' ?></p>
                        <div class="topic-card__foot">Lihat artikel &rarr; <span><?= (int) $k['total_artikel'] ?> artikel</span></div>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </section>
<?php endif ?>

<section class="band band--white" aria-labelledby="judul-artikel">
    <div class="container">
        <div class="sec-head">
            <div>
                <span class="eyebrow">Artikel</span>
                <h2 id="judul-artikel">Tulisan Terbaru</h2>
                <p>Artikel paling baru dari para penulis.</p>
            </div>
            <a class="link-more" href="<?= base_url('artikel') ?>">Lihat semua artikel &rarr;</a>
        </div>

        <?php if (! empty($terbaru)): ?>
            <div class="grid">
                <?php foreach ($terbaru as $art): ?>
                    <?= view('blog/_kartu', ['art' => $art]) ?>
                <?php endforeach ?>
            </div>
        <?php else: ?>
            <div class="empty">
                <h3>Belum ada artikel</h3>
                <p>Artikel yang terbit akan tampil di sini.</p>
                <?php if (auth()->loggedIn()): ?>
                    <a class="btn btn--primary" href="<?= base_url('dashboard/artikel/tambah') ?>">Tulis artikel pertama</a>
                <?php endif ?>
            </div>
        <?php endif ?>
    </div>
</section>
<?= $this->endSection() ?>
