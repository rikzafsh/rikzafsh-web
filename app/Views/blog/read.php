<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php
$user      = auth()->loggedIn() ? auth()->user() : null;
$isStaff   = $user && $user->inGroup('admin', 'developer');
$pemilik   = $user && (int) $user->id === (int) $art['penulis_id'];
$nama      = nama_tampil($art['full_name'] ?? '', $art['username'] ?? '');
$warna     = safe_color($art['kat_colour'] ?? null, '#29aafe');
$urlHalaman = current_url();
$teksBagi  = $art['judul'] . ' - ' . $urlHalaman;

$svgLike    = '<path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.075.265.306 1.039-.55 1.7-.015.012-.03.024-.045.036.03.111.06.223.088.337.072.296.223 1.036-.5 1.722-.058.055-.116.108-.173.16.03.123.056.248.077.375.04.225.12 1.12-.5 1.745-.045.045-.09.088-.135.129C13.518 15.65 12.6 16 11.5 16H8c-1.5 0-3-1.5-3-3V5.5l1.956-3.755zM3 5.5h2v10H3v-10z"/>';
$svgDislike = '<path d="M6.956 14.255c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.075-.266.306-1.04-.55-1.7-.015-.012-.03-.024-.045-.036.03-.111.06-.223.088-.337.072-.296.223-1.036-.5-1.721-.058-.055-.116-.109-.173-.16.03-.123.056-.249.077-.375.04-.224.12-1.12-.5-1.744C13.518.35 12.6 0 11.5 0H8c-1.5 0-3 1.5-3 3v7.5l1.956 3.755zM3 10.5h2V.5H3v10z"/>';
$svgSave    = '<path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/>';

// Menampilkan satu komentar (dan balasannya jika komentar utama)
$tampilKomentar = function (array $k, bool $adalahBalasan) use (&$tampilKomentar, $balasan, $user, $isStaff, $art, $svgLike, $svgDislike) {
    $namaK      = nama_tampil($k['full_name'] ?? '', $k['username'] ?? '');
    $bolehHapus = $user && ($isStaff || (int) $user->id === (int) $k['user_id']);
    $penulisArt = (int) $k['user_id'] === (int) $art['penulis_id'];
    ?>
    <div class="comment" id="komentar-<?= (int) $k['id'] ?>">
        <a href="<?= esc(base_url('profile/' . rawurlencode($k['username'])), 'attr') ?>"><?= avatar_html($k['avatar'] ?? null, $namaK, $adalahBalasan ? 'sm' : 'md') ?></a>
        <div class="comment__main">
            <div class="comment__head">
                <a class="comment__name" href="<?= esc(base_url('profile/' . rawurlencode($k['username'])), 'attr') ?>"><?= esc($namaK) ?></a>
                <?php if ($penulisArt): ?><span class="badge badge--soft">Penulis</span><?php endif ?>
                <span class="comment__time" title="<?= esc(tgl_indo($k['created_at'], true), 'attr') ?>"><?= esc(waktu_lalu($k['created_at'])) ?></span>
            </div>
            <p class="comment__body"><?= esc($k['isi_komentar']) ?></p>

            <div class="comment__actions">
                <?php if ($user): ?>
                    <form action="<?= base_url('komentar/reaksi') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="komentar_id" value="<?= (int) $k['id'] ?>">
                        <input type="hidden" name="jenis" value="like">
                        <button type="submit" class="mini-btn<?= $k['reaksi_user'] === 'like' ? ' is-like' : '' ?>" aria-label="Suka" title="Suka">
                            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgLike ?></svg> <?= (int) $k['total_like'] ?>
                        </button>
                    </form>
                    <form action="<?= base_url('komentar/reaksi') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="komentar_id" value="<?= (int) $k['id'] ?>">
                        <input type="hidden" name="jenis" value="dislike">
                        <button type="submit" class="mini-btn<?= $k['reaksi_user'] === 'dislike' ? ' is-dislike' : '' ?>" aria-label="Tidak suka" title="Tidak suka">
                            <svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgDislike ?></svg> <?= (int) $k['total_dislike'] ?>
                        </button>
                    </form>
                    <?php if (! $adalahBalasan): ?>
                        <button type="button" class="mini-btn" data-reply-toggle="<?= (int) $k['id'] ?>">Balas</button>
                    <?php endif ?>
                <?php else: ?>
                    <span class="mini-btn"><svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgLike ?></svg> <?= (int) $k['total_like'] ?></span>
                    <span class="mini-btn"><svg width="13" height="13" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgDislike ?></svg> <?= (int) $k['total_dislike'] ?></span>
                <?php endif ?>

                <?php if ($bolehHapus): ?>
                    <form action="<?= base_url('komentar/hapus/' . (int) $k['id']) ?>" method="post" data-confirm="<?= esc($adalahBalasan ? 'Hapus balasan ini?' : 'Hapus komentar ini beserta semua balasannya?', 'attr') ?>">
                        <?= csrf_field() ?>
                        <button type="submit" class="mini-btn mini-btn--danger">Hapus</button>
                    </form>
                <?php endif ?>
            </div>

            <?php if (! $adalahBalasan): ?>
                <?php if (! empty($balasan[$k['id']])): ?>
                    <div class="replies">
                        <?php foreach ($balasan[$k['id']] as $b): ?>
                            <?php $tampilKomentar($b, true); ?>
                        <?php endforeach ?>
                    </div>
                <?php endif ?>

                <?php if ($user): ?>
                    <div class="reply-form" id="reply-form-<?= (int) $k['id'] ?>">
                        <form action="<?= base_url('artikel/komentar') ?>" method="post" data-once>
                            <?= csrf_field() ?>
                            <input type="hidden" name="artikel_id" value="<?= (int) $art['id'] ?>">
                            <input type="hidden" name="parent_id" value="<?= (int) $k['id'] ?>">
                            <label class="sr-only" for="balas-<?= (int) $k['id'] ?>">Tulis balasan</label>
                            <textarea class="textarea js-autosize" id="balas-<?= (int) $k['id'] ?>" name="isi_komentar" rows="2" maxlength="1000" placeholder="Tulis balasanmu…" required></textarea>
                            <div class="comment-form__foot">
                                <span></span>
                                <span class="flex gap-8">
                                    <button type="button" class="btn btn--ghost btn--sm" data-reply-cancel>Batal</button>
                                    <button type="submit" class="btn btn--primary btn--sm">Kirim balasan</button>
                                </span>
                            </div>
                        </form>
                    </div>
                <?php endif ?>
            <?php endif ?>
        </div>
    </div>
    <?php
};
?>

<div class="reading-col">
    <nav class="breadcrumb" aria-label="Breadcrumb">
        <a href="<?= base_url('/') ?>">Beranda</a> <span aria-hidden="true">/</span>
        <a href="<?= base_url('artikel') ?>">Artikel</a> <span aria-hidden="true">/</span>
        <?php if (! empty($art['nama_kategori'])): ?>
            <a href="<?= esc(base_url('artikel?kategori=' . rawurlencode((string) $art['kat_slug'])), 'attr') ?>"><?= esc($art['nama_kategori']) ?></a> <span aria-hidden="true">/</span>
        <?php endif ?>
        <span aria-current="page"><?= esc($art['judul']) ?></span>
    </nav>

    <article class="article">
        <?php if (! empty($art['thumbnail'])): ?>
            <img class="article__cover" src="<?= esc(base_url('uploads/artikel/' . $art['thumbnail']), 'attr') ?>" alt="Gambar sampul: <?= esc($art['judul'], 'attr') ?>">
        <?php endif ?>

        <div class="article__inner">
            <?php if (! $terbit): ?>
                <div class="draft-banner">Ini masih berupa draf. Hanya kamu (dan admin) yang bisa melihat halaman ini.</div>
            <?php endif ?>

            <?php if (! empty($art['nama_kategori'])): ?>
                <a class="badge" style="background: <?= $warna ?>" href="<?= esc(base_url('artikel?kategori=' . rawurlencode((string) $art['kat_slug'])), 'attr') ?>"><?= esc($art['nama_kategori']) ?></a>
            <?php endif ?>

            <h1 class="article__title"><?= esc($art['judul']) ?></h1>

            <div class="article__meta">
                <div class="byline">
                    <a href="<?= esc(base_url('profile/' . rawurlencode($art['username'] ?? '')), 'attr') ?>"><?= avatar_html($art['avatar'] ?? null, $nama, 'md') ?></a>
                    <div>
                        <a class="byline__name" href="<?= esc(base_url('profile/' . rawurlencode($art['username'] ?? '')), 'attr') ?>"><?= esc($nama) ?></a>
                        <span class="byline__sub">
                            <span title="<?= esc(tgl_indo($art['created_at'], true), 'attr') ?>"><?= esc(tgl_indo($art['created_at'])) ?></span>
                            <span><?= waktu_baca($art['konten']) ?> menit baca</span>
                            <span><?= number_format($art['views']) ?> dibaca</span>
                            <span><?= (int) $total_komentar ?> komentar</span>
                        </span>
                    </div>
                </div>

                <?php if ($pemilik || $isStaff): ?>
                    <a class="btn btn--ghost btn--sm" href="<?= base_url('dashboard/artikel/edit/' . (int) $art['id']) ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                        Edit artikel
                    </a>
                <?php endif ?>
            </div>

            <div class="prose" id="articleBody">
                <?= sanitize_html($art['konten']) ?>
            </div>

            <div class="reaction-bar" id="reaksi">
                <?php if ($user): ?>
                    <form action="<?= base_url('artikel/reaksi') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="artikel_id" value="<?= (int) $art['id'] ?>">
                        <input type="hidden" name="jenis" value="like">
                        <button type="submit" class="react-btn<?= $art['reaksi_user'] === 'like' ? ' is-like' : '' ?>" aria-pressed="<?= $art['reaksi_user'] === 'like' ? 'true' : 'false' ?>">
                            <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgLike ?></svg> Suka <span><?= (int) $art['total_like'] ?></span>
                        </button>
                    </form>
                    <form action="<?= base_url('artikel/reaksi') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="artikel_id" value="<?= (int) $art['id'] ?>">
                        <input type="hidden" name="jenis" value="dislike">
                        <button type="submit" class="react-btn<?= $art['reaksi_user'] === 'dislike' ? ' is-dislike' : '' ?>" aria-pressed="<?= $art['reaksi_user'] === 'dislike' ? 'true' : 'false' ?>">
                            <svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgDislike ?></svg> Kurang suka <span><?= (int) $art['total_dislike'] ?></span>
                        </button>
                    </form>
                    <form action="<?= base_url('artikel/simpan') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="artikel_id" value="<?= (int) $art['id'] ?>">
                        <button type="submit" class="react-btn<?= $art['is_saved'] > 0 ? ' is-saved' : '' ?>" aria-pressed="<?= $art['is_saved'] > 0 ? 'true' : 'false' ?>" title="<?= $art['is_saved'] > 0 ? 'Batal simpan artikel' : 'Simpan artikel ini' ?>">
                            <svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgSave ?></svg> <?= $art['is_saved'] > 0 ? 'Tersimpan' : 'Simpan' ?> <span><?= (int) $art['total_simpan'] ?></span>
                        </button>
                    </form>
                <?php else: ?>
                    <a class="react-btn" href="<?= base_url('login') ?>"><svg width="15" height="15" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgLike ?></svg> Suka <span><?= (int) $art['total_like'] ?></span></a>
                    <a class="react-btn" href="<?= base_url('login') ?>"><svg width="14" height="14" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><?= $svgSave ?></svg> Simpan <span><?= (int) $art['total_simpan'] ?></span></a>
                <?php endif ?>

                <span class="reaction-bar__spacer"></span>

                <div class="share">
                    <span class="share__label hide-mobile">Bagikan</span>
                    <button type="button" class="icon-btn" data-copy title="Salin tautan" aria-label="Salin tautan">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
                    </button>
                    <a class="icon-btn" target="_blank" rel="noopener noreferrer" title="Bagikan ke WhatsApp" aria-label="Bagikan ke WhatsApp" href="<?= esc('https://wa.me/?text=' . rawurlencode($teksBagi), 'attr') ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 11.5a8.4 8.4 0 0 1-12.4 7.4L3 20l1.2-5.4A8.4 8.4 0 1 1 21 11.5z"/></svg>
                    </a>
                    <a class="icon-btn" target="_blank" rel="noopener noreferrer" title="Bagikan ke X" aria-label="Bagikan ke X" href="<?= esc('https://twitter.com/intent/tweet?text=' . rawurlencode($art['judul']) . '&url=' . rawurlencode($urlHalaman), 'attr') ?>">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M4 4l16 16M20 4L4 20"/></svg>
                    </a>
                </div>
            </div>

            <?php if (! empty($art['username'])): ?>
                <div class="author-box">
                    <a href="<?= esc(base_url('profile/' . rawurlencode($art['username'])), 'attr') ?>"><?= avatar_html($art['avatar'] ?? null, $nama, 'lg') ?></a>
                    <div>
                        <span class="text-muted" style="font-size: .78rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;">Ditulis oleh</span>
                        <h4><a href="<?= esc(base_url('profile/' . rawurlencode($art['username'])), 'attr') ?>" style="color: inherit;"><?= esc($nama) ?></a></h4>
                        <p><?= ! empty($art['bio']) ? esc(mb_strimwidth($art['bio'], 0, 220, '…', 'UTF-8')) : 'Penulis ini belum menuliskan bio.' ?></p>
                        <a class="link-more" href="<?= esc(base_url('artikel?penulis=' . rawurlencode($art['username'])), 'attr') ?>">Lihat semua tulisannya &rarr;</a>
                    </div>
                </div>
            <?php endif ?>

            <section class="comments" id="diskusi" aria-labelledby="judul-diskusi">
                <h2 class="comments__title" id="judul-diskusi">Diskusi <span class="comments__count"><?= (int) $total_komentar ?></span></h2>

                <?php if ($user): ?>
                    <form class="comment-form" action="<?= base_url('artikel/komentar') ?>" method="post" data-once>
                        <?= csrf_field() ?>
                        <?= avatar_html($user->avatar ?? null, nama_tampil($user->full_name ?? '', $user->username), 'md') ?>
                        <div class="comment-form__box">
                            <input type="hidden" name="artikel_id" value="<?= (int) $art['id'] ?>">
                            <label class="sr-only" for="isi_komentar">Komentar</label>
                            <textarea class="textarea js-autosize" id="isi_komentar" name="isi_komentar" rows="3" maxlength="1000" data-counter="#hitungKomentar" placeholder="Tulis opini atau pertanyaanmu di sini…" required><?= esc(old('isi_komentar') ?? '') ?></textarea>
                            <div class="comment-form__foot">
                                <span class="counter" id="hitungKomentar">0 / 1000</span>
                                <button type="submit" class="btn btn--primary btn--sm">Kirim komentar</button>
                            </div>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="login-prompt">
                        <p>Masuk untuk ikut berdiskusi dan memberi reaksi.</p>
                        <a class="btn btn--primary btn--sm" href="<?= base_url('login') ?>">Masuk</a>
                    </div>
                <?php endif ?>

                <?php if (! empty($komentar)): ?>
                    <?php foreach ($komentar as $k): ?>
                        <?php $tampilKomentar($k, false); ?>
                    <?php endforeach ?>
                <?php else: ?>
                    <div class="empty" style="padding: 32px 20px;">
                        <h3 style="font-size: 1.05rem;">Belum ada komentar</h3>
                        <p class="mb-0">Jadilah yang pertama memberi tanggapan.</p>
                    </div>
                <?php endif ?>
            </section>
        </div>
    </article>
</div>

<?php if (! empty($terkait)): ?>
    <h2 class="related-title">Baca juga</h2>
    <div class="grid">
        <?php foreach ($terkait as $rel): ?>
            <?= view('blog/_kartu', ['art' => $rel]) ?>
        <?php endforeach ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
