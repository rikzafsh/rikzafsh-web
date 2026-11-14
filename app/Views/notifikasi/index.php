<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="reading-col">
    <div class="page-head">
        <div>
            <h1>Notifikasi</h1>
            <p>Aktivitas pada artikel dan komentarmu.</p>
        </div>

        <?php if ($tersedia && ! empty($daftar)): ?>
            <div class="flex gap-8" style="flex-wrap: wrap;">
                <?php if ($belum_dibaca > 0): ?>
                    <form action="<?= base_url('notifikasi/baca-semua') ?>" method="post" data-once>
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn--ghost btn--sm">Tandai semua dibaca (<?= (int) $belum_dibaca ?>)</button>
                    </form>
                <?php endif ?>
                <form action="<?= base_url('notifikasi/hapus-dibaca') ?>" method="post" data-confirm="Hapus semua notifikasi yang sudah dibaca?">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn--danger-ghost btn--sm">Hapus yang sudah dibaca</button>
                </form>
            </div>
        <?php endif ?>
    </div>

    <?php if (! $tersedia): ?>
        <div class="alert alert--info" role="status">Fitur notifikasi belum aktif. Jalankan <code>php spark migrate</code> sekali untuk mengaktifkannya.</div>
    <?php elseif (empty($daftar)): ?>
        <div class="empty">
            <h3>Belum ada notifikasi</h3>
            <p class="mb-0">Kamu akan diberi tahu saat ada yang menyukai atau mengomentari tulisanmu.</p>
        </div>
    <?php else: ?>
        <div class="notif-list">
            <?php foreach ($daftar as $n): ?>
                <?= view('notifikasi/_item', ['n' => $n]) ?>
            <?php endforeach ?>
        </div>
        <?= $pager->links('default', 'ruang_rikza') ?>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
