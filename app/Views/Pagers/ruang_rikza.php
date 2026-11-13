<?php

/**
 * Template pagination Ruang Rikza.
 *
 * @var \CodeIgniter\Pager\PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<?php if ($pager->getPageCount() > 1): ?>
<nav class="pagination" aria-label="Navigasi halaman">
    <?php if ($pager->hasPrevious()): ?>
        <a class="pagination__item pagination__item--nav" href="<?= $pager->getPrevious() ?>" rel="prev" aria-label="Halaman sebelumnya">&larr; <span>Sebelumnya</span></a>
    <?php endif ?>

    <?php foreach ($pager->links() as $link): ?>
        <a class="pagination__item<?= $link['active'] ? ' is-active' : '' ?>" href="<?= $link['uri'] ?>"<?= $link['active'] ? ' aria-current="page"' : '' ?>><?= $link['title'] ?></a>
    <?php endforeach ?>

    <?php if ($pager->hasNext()): ?>
        <a class="pagination__item pagination__item--nav" href="<?= $pager->getNext() ?>" rel="next" aria-label="Halaman berikutnya"><span>Berikutnya</span> &rarr;</a>
    <?php endif ?>
</nav>
<?php endif ?>
