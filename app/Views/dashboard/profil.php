<?= $this->extend('layout/dashboard_template') ?>

<?= $this->section('dashboard_content') ?>
<div class="dashboard-container" style="padding: 30px; max-width: 1050px; box-sizing: border-box; font-family: system-ui, -apple-system, sans-serif;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0; font-size: 1.5rem; color: #1e293b; font-weight: 700;">Profil Saya</h2>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.88rem;">Kelola informasi profil, foto avatar, dan bio Anda.</p>
    </div>
    
    <?php if (session()->getFlashdata('message')) : ?>
        <div style="padding: 12px; background: #d1e7dd; color: #0f5132; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem;">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 350px 1fr; gap: 30px; align-items: start;">
        
        <div style="background: #ffffff; padding: 35px 20px; border: 1px solid #e3e6f0; border-radius: 12px; text-align: center; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <div style="position: relative; display: inline-block; margin-bottom: 15px;">
                <?php 
                    // Jika user punya file foto di database, pakai itu. Jika tidak, pakai UI-Avatars.
                    $avatarUrl = (!empty($user->avatar) && file_exists(FCPATH . 'uploads/avatars/' . $user->avatar)) 
                        ? base_url('uploads/avatars/' . $user->avatar) 
                        : 'https://ui-avatars.com/api/?name=' . urlencode($user->username) . '&background=29aafe&color=fff&bold=true&size=110';
                ?>
                <img src="<?= esc($avatarUrl) ?>" alt="Avatar" style="width: 110px; height: 110px; border-radius: 50%; border: 4px solid #f1f3f9; object-fit: cover;">
            </div>
            <h3 style="margin: 0 0 4px 0; font-size: 1.2rem; color: #1e293b; font-weight: 700;"><?= esc($user->full_name ?? 'User Baru') ?></h3>
            <span style="color: #858796; font-size: 0.85rem; display: block; margin-bottom: 15px;">@<?= esc($user->username) ?></span>
            
            <div style="display: flex; justify-content: center; gap: 6px; margin-bottom: 15px;">
                <?php if ($user->inGroup('developer')): ?>
                    <span style="background: rgba(188, 132, 238, 0.15); color: #703996; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: bold; text-transform: uppercase;">Developer</span>
                <?php elseif ($user->inGroup('admin')): ?>
                    <span style="background: rgba(41, 170, 254, 0.15); color: #3a43e0; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: bold; text-transform: uppercase;">Admin</span>
                <?php elseif ($user->inGroup('superuser')): ?>
                    <span style="background: rgba(245, 158, 11, 0.15); color: #009339; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: bold; text-transform: uppercase;">Superuser</span>
                <?php else: ?>
                    <span style="background: rgba(100, 116, 139, 0.15); color: #64748b; padding: 4px 12px; border-radius: 20px; font-size: 0.72rem; font-weight: bold; text-transform: uppercase;">User</span>
                <?php endif; ?>
            </div>

            <p style="font-size: 0.82rem; color: #64748b; line-height: 1.4; margin: 0; font-style: italic;">
                <?php if(!empty($user->bio)): ?>
                            <?= nl2br(esc($user->bio)) ?>
                        <?php else: ?>
                            <em style="color: #999;">Pengguna ini belum menuliskan bio.</em>
                        <?php endif; ?>
            </p>
        </div>
        
        <div style="background: #ffffff; padding: 30px; border: 1px solid #e3e6f0; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.02);">
            <form action="<?= base_url('dashboard/profil/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #4e73df; margin-bottom: 8px;">Username</label>
                        <input type="text" value="<?= esc($user->username) ?>" disabled style="width: 100%; padding: 10px 12px; background: #f8f9fc; border: 1px solid #d1d3e2; border-radius: 6px; color: #858796; cursor: not-allowed; box-sizing: border-box; font-size: 0.9rem;">
                    </div>
                    <div>
                        <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #4e73df; margin-bottom: 8px;">Alamat Email</label>
                        <input type="email" value="<?= esc($user->email) ?>" disabled style="width: 100%; padding: 10px 12px; background: #f8f9fc; border: 1px solid #d1d3e2; border-radius: 6px; color: #858796; cursor: not-allowed; box-sizing: border-box; font-size: 0.9rem;">
                    </div>
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #3a3b45; margin-bottom: 8px;">Nama Lengkap</label>
                    <input type="text" name="full_name" value="<?= esc($user->full_name ?? '') ?>" required style="width: 100%; padding: 11px 12px; border: 1px solid #d1d3e2; border-radius: 6px; color: #343a40; font-size: 0.9rem; box-sizing: border-box;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #3a3b45; margin-bottom: 8px;">Ganti Foto Profil</label>
                    <input type="file" name="avatar" accept="image/*" style="width: 100%; padding: 8px 12px; border: 1px solid #d1d3e2; border-radius: 6px; font-size: 0.88rem; box-sizing: border-box; background: #fff;">
                    <small style="color: #a1a1a1; display: block; margin-top: 5px; font-size: 0.75rem;">Format: JPG, JPEG, PNG (Maks. 2MB)</small>
                </div>

                <div style="margin-bottom: 30px;">
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; color: #3a3b45; margin-bottom: 8px;">Bio Singkat</label>
                    <textarea name="bio" rows="3" placeholder="Ceritakan sedikit tentang diri Anda..." style="width: 100%; padding: 11px 12px; border: 1px solid #d1d3e2; border-radius: 6px; color: #343a40; font-size: 0.9rem; box-sizing: border-box; resize: vertical; font-family: inherit;"><?= esc($user->bio ?? '') ?></textarea>
                </div>
                
                <hr style="border: 0; border-top: 1px solid #eaecf4; margin: 0 0 25px 0;">
                
                <div style="display: flex; justify-content: flex-end;">
                    <button type="submit" style="background: var(--theme-primary, #6f42c1); color: #ffffff; padding: 11px 24px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; font-size: 0.9rem; box-shadow: 0 4px 6px rgba(111, 66, 193, 0.15);">
                        Simpan Perubahan
                    </button>
                </div>
                
            </form>
        </div>
        
    </div>
</div>
<?= $this->endSection() ?>