<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div style="max-width: 800px; margin: 40px auto; font-family: sans-serif;">
    
    <div style="background: #fff; padding: 40px; border-radius: 12px 12px 0 0; box-shadow: 0 4px 6px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 30px; border-bottom: 1px solid #eee;">
        <?php 
            $avatarProfile = $user->avatar ?? '';
            if ($avatarProfile !== '' && file_exists(FCPATH . 'uploads/avatars/' . $avatarProfile)) {
                $fotoProfile = base_url('uploads/avatars/' . $avatarProfile);
            } else {
                $namaProfile = $user->full_name ?? $user->username;
                $fotoProfile = "https://ui-avatars.com/api/?name=" . urlencode($namaProfile) . "&size=120&background=random&bold=true"; 
            }
        ?>
        <img src="<?= esc($fotoProfile) ?>" alt="Foto Profil" style="border-radius: 50%; border: 4px solid #f0f4f8; width: 120px; height: 120px; object-fit: cover; flex-shrink: 0;">
        
        <div>
            <h1 style="margin: 0 0 5px 0; color: var(--theme-primary, #333); font-size: 2.2em;"><?= esc($user->full_name ?? $user->username) ?></h1>
            <p style="color: #777; margin: 0 0 12px 0; font-size: 1.1em;">@<?= esc($user->username) ?></p>
            
            <?php 
                // Mengambil daftar role pengguna dari CodeIgniter Shield
                $roles = $user->getGroups(); 
                if (!empty($roles)): 
            ?>
                <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                    <?php foreach($roles as $role): ?>
                        <span style="background: #e3f2fd; color: #1976d2; padding: 5px 12px; border-radius: 20px; font-size: 0.85em; font-weight: bold; text-transform: capitalize;">
                            <?= esc($role) ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div style="background: #fdfdfd; padding: 40px; border-radius: 0 0 12px 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
            
            <div>
                <h3 style="color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 0;">Tentang Pengguna</h3>
                
                <div style="margin-bottom: 20px;">
                    <strong style="color: #555; font-size: 0.9em; text-transform: uppercase;">Bergabung Sejak</strong><br>
                    <span style="color: #333;"><?= date('d F Y', strtotime($user->created_at)) ?></span>
                </div>

                <div style="margin-bottom: 20px;">
                    <strong style="color: #555; font-size: 0.9em; text-transform: uppercase;">Bio</strong><br>
                    <div style="font-size: 0.85em; color: #444; line-height: 1.6; padding: 0px; border-radius: 8px; margin-top: 5px;">
                        <?php if(!empty($user->bio)): ?>
                            <?= nl2br(esc($user->bio)) ?>
                        <?php else: ?>
                            <em style="color: #999;">Pengguna ini belum menuliskan bio.</em>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-top: 0;">Artikel Terbaru</h3>
                
                <?php if(!empty($artikelTerbaru)): ?>
                    <div style="display: flex; flex-direction: column; gap: 15px; margin-top: 15px;">
                        <?php foreach($artikelTerbaru as $art): ?>
                            <a href="<?= base_url('artikel/' . esc($art['slug'])) ?>" style="text-decoration: none; color: inherit; display: block; background: #fff; padding: 15px; border-radius: 8px; border: 1px solid #eee; transition: all 0.2s ease;" onmouseover="this.style.borderColor='var(--theme-primary, #007bff)'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 10px rgba(0,0,0,0.05)'" onmouseout="this.style.borderColor='#eee'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                <h4 style="margin: 0 0 5px 0; color: #222; font-size: 1.05em; line-height: 1.4;"><?= esc($art['judul']) ?></h4>
                                <span style="font-size: 0.85em; color: #888;"><?= date('d M Y', strtotime($art['created_at'])) ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div style="background: #fff; padding: 20px; border-radius: 8px; border: 1px dashed #ccc; text-align: center; color: #777; margin-top: 15px;">
                        Belum ada artikel yang dipublikasikan.
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <div style="margin-top: 40px; text-align: center;">
            <a href="javascript:history.back()" style="display: inline-block; padding: 10px 25px; background: #eee; color: #333; text-decoration: none; border-radius: 6px; font-weight: bold; transition: background 0.2s;" onmouseover="this.style.background='#ddd'" onmouseout="this.style.background='#eee'">⬅ Kembali</a>
        </div>

    </div>

</div>
<?= $this->endSection() ?>