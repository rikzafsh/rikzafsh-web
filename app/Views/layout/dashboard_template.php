<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<?php
$ikon = [
    'dashboard' => '<rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect>',
    'artikel'   => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line>',
    'profil'    => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>',
    'users'     => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>',
    'proyek'    => '<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>',
];
$menu = [
    ['url' => 'dashboard',          'pola' => 'dashboard',          'label' => 'Ringkasan',      'ikon' => 'dashboard', 'dev' => false],
    ['url' => 'dashboard/artikel',  'pola' => 'dashboard/artikel*', 'label' => 'Kelola Artikel', 'ikon' => 'artikel',   'dev' => false],
    ['url' => 'dashboard/profil',   'pola' => 'dashboard/profil*',  'label' => 'Profil Saya',    'ikon' => 'profil',    'dev' => false],
    ['url' => 'dashboard/users',    'pola' => 'dashboard/users*',   'label' => 'Pengguna',       'ikon' => 'users',     'dev' => true],
    ['url' => 'dashboard/projects', 'pola' => 'dashboard/projects*', 'label' => 'Proyek',        'ikon' => 'proyek',    'dev' => true],
];
$isDev = auth()->user()->inGroup('developer');
?>
<div class="dash">
    <aside class="dash__side" aria-label="Menu dasbor">
        <p class="dash__side-title">Menu</p>
        <nav class="dash__nav">
            <?php foreach ($menu as $m): ?>
                <?php if ($m['dev'] && ! $isDev) { continue; } ?>
                <a class="dash__link<?= url_is($m['pola']) ? ' is-active' : '' ?>" href="<?= base_url($m['url']) ?>"<?= url_is($m['pola']) ? ' aria-current="page"' : '' ?>>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?= $ikon[$m['ikon']] ?></svg>
                    <?= esc($m['label']) ?>
                </a>
            <?php endforeach ?>
        </nav>
        <div class="dash__side-cta">
            <a class="btn btn--primary btn--block" href="<?= base_url('dashboard/artikel/tambah') ?>">+ Tulis Artikel</a>
        </div>
    </aside>

    <section class="dash__main">
        <?= $this->include('layout/flash') ?>
        <?= $this->renderSection('dashboard_content') ?>
    </section>
</div>
<?= $this->endSection() ?>
