<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php
$nama     = nama_tampil($user->full_name ?? '', $user->username);
$roles    = $user->getGroups();
$sendiri  = auth()->loggedIn() && (int) auth()->id() === (int) $user->id;
?>

<section class="card card--pad" style="display: flex; gap: 28px; align-items: center; flex-wrap: wrap;">
    <?= avatar_html($user->avatar ?? null, $nama, 'xl') ?>

    <div style="flex: 1; min-width: 240px;">
        <h1 style="margin: 0 0 2px; font-size: 1.9rem; font-weight: 800;"><?= esc($nama) ?></h1>
        <p class="text-muted" style="margin: 0 0 10px;">@<?= esc($user->username) ?></p>

        <?php if (! empty($roles)): ?>
            <div class="flex gap-8" style="flex-wrap: wrap; margin-bottom: 12px;">
                <?php foreach ($roles as $role): ?>
                    <span class="badge badge--soft"><?= esc($role) ?></span>
                <?php endforeach ?>
            </div>
        <?php endif ?>

        <p style="max-width: 640px; color: #4a4f68; margin: 0 0 14px;">
            <?php if (! empty($user->bio)): ?>
                <?= nl2br(esc($user->bio)) ?>
            <?php else: ?>
                <em class="text-muted">Pengguna ini belum menuliskan bio.</em>
            <?php endif ?>
        </p>

        <div class="flex gap-8" style="flex-wrap: wrap; align-items: center;">
            <span class="text-muted" style="font-size: .9rem;">Bergabung <?= esc(tgl_indo($user->created_at)) ?> &middot; <?= number_format($totalArtikel) ?> artikel</span>
            <?php if ($sendiri): ?>
                <a class="btn btn--ghost btn--sm" href="<?= base_url('dashboard/profil') ?>">Edit profil</a>
            <?php endif ?>
        </div>
    </div>
</section>

<section class="section" aria-labelledby="judul-tulisan">
    <div class="section__head">
        <h2 class="section__title" id="judul-tulisan">Tulisan terbaru</h2>
        <?php if ($totalArtikel > count($artikelTerbaru)): ?>
            <a class="link-more" href="<?= esc(base_url('artikel?penulis=' . rawurlencode($user->username)), 'attr') ?>">Lihat semua (<?= number_format($totalArtikel) ?>) &rarr;</a>
        <?php endif ?>
    </div>

    <?php if (! empty($artikelTerbaru)): ?>
        <div class="grid">
            <?php foreach ($artikelTerbaru as $art): ?>
                <?= view('blog/_kartu', ['art' => $art]) ?>
            <?php endforeach ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <h3>Belum ada artikel</h3>
            <p class="mb-0">Belum ada artikel yang dipublikasikan oleh pengguna ini.</p>
        </div>
    <?php endif ?>
</section>
<?= $this->endSection() ?>
