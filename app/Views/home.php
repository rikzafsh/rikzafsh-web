<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php
$hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
$bulan = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$hariIni = $hari[(int) date('w')] . ', ' . date('j') . ' ' . $bulan[(int) date('n')] . ' ' . date('Y');

$unggulan = $terbaru[0] ?? null;
$sisa     = array_slice($terbaru, 1, 6);
?>

<section class="intro">
    <div class="intro__inner">
        <span class="intro__date"><?= esc($hariIni) ?></span>
        <h1>Tulisan, proyek, dan catatan belajar.</h1>
        <p>Selamat datang di Ruang Rikza. Baca artikel terbaru, atau lihat proyek yang sedang dikerjakan.</p>
        <div class="intro__links">
            <a href="<?= base_url('artikel') ?>">Jelajahi artikel &rarr;</a>
            <a href="<?= base_url('projects') ?>">Lihat proyek &rarr;</a>
        </div>
    </div>
</section>

<div class="container">
    <div class="home-grid">

        <section aria-labelledby="judul-terbaru">
            <h2 class="block-title" id="judul-terbaru">Terbaru</h2>

            <?php if ($unggulan): ?>
                <?= view('blog/_unggulan', ['art' => $unggulan]) ?>

                <?php if (! empty($sisa)): ?>
                    <div class="post-list">
                        <?php foreach ($sisa as $art): ?>
                            <?= view('blog/_baris', ['art' => $art]) ?>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <p style="margin-top: 18px;"><a class="link-more" href="<?= base_url('artikel') ?>">Lihat semua artikel (<?= number_format($total_artikel) ?>) &rarr;</a></p>
            <?php else: ?>
                <div class="empty">
                    <h3>Belum ada artikel</h3>
                    <p>Jadilah yang pertama menulis di sini.</p>
                    <a class="btn btn--primary" href="<?= base_url(auth()->loggedIn() ? 'dashboard/artikel/tambah' : 'login') ?>"><?= auth()->loggedIn() ? 'Tulis artikel pertama' : 'Masuk untuk menulis' ?></a>
                </div>
            <?php endif ?>
        </section>

        <aside aria-label="Informasi tambahan">
            <?php if (! empty($populer)): ?>
                <div class="side-box">
                    <h2 class="block-title block-title--small">Paling banyak dibaca</h2>
                    <ol class="side-list" style="list-style: none; padding: 0;">
                        <?php foreach ($populer as $i => $p): ?>
                            <li>
                                <a class="rank-item" href="<?= esc(base_url('artikel/' . $p['slug']), 'attr') ?>">
                                    <span class="rank-item__no"><?= $i + 1 ?></span>
                                    <span>
                                        <span class="rank-item__title"><?= esc($p['judul']) ?></span>
                                        <span class="rank-item__meta"><?= number_format($p['views']) ?> dibaca &middot; <?= esc(tgl_indo($p['created_at'])) ?></span>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ol>
                </div>
            <?php endif ?>

            <?php if (! empty($kategori)): ?>
                <div class="side-box">
                    <h2 class="block-title block-title--small">Kategori</h2>
                    <ul class="side-list">
                        <?php foreach ($kategori as $k): ?>
                            <li>
                                <a class="cat-item" style="--kat: <?= safe_color($k['colour'] ?? null, '#29aafe') ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode($k['slug'])), 'attr') ?>">
                                    <span><?= esc($k['nama_kategori']) ?></span>
                                    <small><?= (int) $k['total_artikel'] ?></small>
                                </a>
                            </li>
                        <?php endforeach ?>
                    </ul>
                </div>
            <?php endif ?>

            <div class="note-box">
                <?php if (auth()->loggedIn()): ?>
                    <strong>Punya ide untuk dibagikan?</strong>
                    <p>Tulis artikel baru. Kamu bisa menyimpannya sebagai draf dulu.</p>
                    <a class="link-more" href="<?= base_url('dashboard/artikel/tambah') ?>">Tulis artikel &rarr;</a>
                <?php else: ?>
                    <strong>Ingin ikut menulis?</strong>
                    <p>Masuk atau daftar untuk menulis, berkomentar, dan menyimpan bacaan.</p>
                    <a class="link-more" href="<?= base_url('login') ?>">Masuk atau daftar &rarr;</a>
                <?php endif ?>
            </div>
        </aside>

    </div>
</div>
<?= $this->endSection() ?>
