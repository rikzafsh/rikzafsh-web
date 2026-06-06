<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div style="display: flex; min-height: calc(100vh - 110px); background-color: #f8f9fa; width: 100%;">
    
    <div style="width: 260px; background-color: #ffffff; border-right: 1px solid #e3e6f0; padding: 30px 15px; flex-shrink: 0; box-sizing: border-box;">
        <h3 style="margin-top: 0; margin-bottom: 20px; padding-left: 12px; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #a1a1a1; font-weight: bold;">Menu Navigasi</h3>
        <nav>
            <a href="/dashboard" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #495057; text-decoration: none; border-radius: 6px; margin-bottom: 6px; font-weight: 500; <?= url_is('dashboard') ? 'background-color: var(--theme-primary, #6f42c1); color: #ffffff; font-weight: bold;' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9"></rect><rect x="14" y="3" width="7" height="5"></rect><rect x="14" y="12" width="7" height="9"></rect><rect x="3" y="16" width="7" height="5"></rect></svg>
                Dashboard
            </a>
            <a href="/dashboard/artikel" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #495057; text-decoration: none; border-radius: 6px; margin-bottom: 6px; font-weight: 500; <?= url_is('dashboard/artikel*') ? 'background-color: var(--theme-primary, #6f42c1); color: #ffffff; font-weight: bold;' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                Kelola Artikel
            </a>
            <a href="/dashboard/profil" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #495057; text-decoration: none; border-radius: 6px; margin-bottom: 6px; font-weight: 500; <?= url_is('dashboard/profil*') ? 'background-color: var(--theme-primary, #6f42c1); color: #ffffff; font-weight: bold;' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Profil Saya
            </a>
            
            <?php if (auth()->user()->inGroup('developer')): ?>
            <a href="/dashboard/users" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #495057; text-decoration: none; border-radius: 6px; margin-bottom: 6px; font-weight: 500; <?= url_is('dashboard/users*') ? 'background-color: var(--theme-primary, #6f42c1); color: #ffffff; font-weight: bold;' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                Kelola Pengguna
            </a>
            
            <a href="/dashboard/projects" style="display: flex; align-items: center; gap: 12px; padding: 12px; color: #495057; text-decoration: none; border-radius: 6px; margin-bottom: 6px; font-weight: 500; <?= url_is('dashboard/projects*') ? 'background-color: var(--theme-primary, #6f42c1); color: #ffffff; font-weight: bold;' : '' ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                Kelola Proyek
            </a>
            <?php endif; ?>
        </nav>
    </div>

    <div style="flex-grow: 1; padding: 40px; background-color: #f8f9fa; box-sizing: border-box;">
        <?= $this->renderSection('dashboard_content') ?>
    </div>

</div>
<?= $this->endSection() ?>