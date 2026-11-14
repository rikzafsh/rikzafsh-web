<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<section class="page-hero">
    <div class="page-hero__inner">
        <h1>Pustaka Karya</h1>
        <p>Seluruh edisi publikasi yang tersedia, dari yang terbaru.</p>
    </div>
</section>

<div class="container pad-bottom" style="padding-top: 36px;">
    <?php if (! empty($majalah)): ?>
        <div class="proj-grid">
            <?php foreach ($majalah as $mjl): ?>
                <?= view('projects/_kartu', ['mjl' => $mjl]) ?>
            <?php endforeach ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <h3>Belum ada karya</h3>
            <p class="mb-0">Edisi yang dipublikasikan akan tampil di sini.</p>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
