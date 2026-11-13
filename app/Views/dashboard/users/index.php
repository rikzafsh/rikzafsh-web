<?= $this->extend('layout/dashboard_template') ?>

<?= $this->section('dashboard_content') ?>
<div style="background: #ffffff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 30px;">
    
    <div style="margin-bottom: 25px;">
        <h2 style="margin: 0; color: #1e293b; font-size: 1.6rem; font-weight: 700;">Kelola Pengguna</h2>
        <p style="margin: 5px 0 0 0; color: #64748b; font-size: 0.9rem;">Panel kendali Developer untuk mengatur hak akses seluruh pengguna.</p>
    </div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div style="padding: 15px; background: #d1e7dd; color: #0f5132; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border-left: 5px solid #198754;">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.88rem;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
                    <th style="padding: 15px;">Pengguna</th>
                    <th style="padding: 15px;">Email</th>
                    <th style="padding: 15px;">Terakhir Aktif</th>
                    <th style="padding: 15px;">Bergabung</th>
                    <th style="padding: 15px;">Role Saat Ini</th>
                    <th style="padding: 15px; text-align: center;">Ubah Role</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u) : ?>
                <tr style="border-bottom: 1px solid #f1f5f9; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#fcfcfc'" onmouseout="this.style.backgroundColor='transparent'">
                    <td style="padding: 12px 15px;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <?php 
                                $avatar = (!empty($u->avatar) && file_exists(FCPATH . 'uploads/avatars/' . $u->avatar)) 
                                ? base_url('uploads/avatars/' . $u->avatar) 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($u->username) . '&background=random&color=fff';
                                ?>
                            <a href="/profile/<?= esc($u->username) ?>">
                                <img src="<?= esc($avatar) ?>" alt="Avatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #eee;">
                            </a>
                            <div>
                                <a href="/profile/<?= esc($u->username) ?>" style="text-decoration: none;">
                                <div style="font-weight: 700; color: #1e293b;"><?= esc($u->full_name ?? 'Belum Diatur') ?></div>
                                <div style="color: #64748b; font-size: 0.8rem;">@<?= esc($u->username) ?></div>
                                </a>
                            </div>
                        </div>
                    </td>
                    <td style="padding: 15px; color: #475569;"><?= esc($u->email) ?></td>
                    <td style="padding: 15px; color: #64748b;">
                        <?= $u->last_active ? $u->last_active->humanize() : '<span style="color:#cbd5e1;">Belum ada aktivitas</span>' ?>
                    </td>
                    <td style="padding: 15px; color: #64748b;">
                        <?= date('d M Y', strtotime($u->created_at)) ?>
                    </td>
                    <td style="padding: 15px;">
                        <?php if ($u->inGroup('developer')): ?>
                            <span style="background: #703996; color: #ffffff; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid #d8b4fe;">DEVELOPER</span>
                        <?php elseif ($u->inGroup('admin')): ?>
                            <span style="background: #3a43e0; color: #ffffff; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid #7dd3fc;">ADMIN</span>
                        <?php elseif ($u->inGroup('superuser')): ?>
                            <span style="background: #009339; color: #ffffff; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid #fcd34d;">SUPERUSER</span>
                        <?php else: ?>
                            <span style="background: #475569; color: #ffffff; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; border: 1px solid #e2e8f0;">USER</span>
                        <?php endif; ?>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <?php if ($u->id !== auth()->id()) : ?>
                        <div style="display: flex; gap: 5px; justify-content: center;">
                            <form action="<?= base_url('dashboard/users/update-role/'.$u->id) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role" value="developer">
                                <button title="Jadikan Developer" style="cursor:pointer; background:#703996; color:white; border:none; padding:6px; border-radius:4px; font-size:10px; font-weight:bold;">DEV</button>
                            </form>
                            <form action="<?= base_url('dashboard/users/update-role/'.$u->id) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role" value="admin">
                                <button title="Jadikan Admin" style="cursor:pointer; background:#3a43e0; color:white; border:none; padding:6px; border-radius:4px; font-size:10px; font-weight:bold;">ADM</button>
                            </form>
                            <form action="<?= base_url('dashboard/users/update-role/'.$u->id) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role" value="superuser">
                                <button title="Jadikan Superuser" style="cursor:pointer; background:#009339; color:white; border:none; padding:6px; border-radius:4px; font-size:10px; font-weight:bold;">SUP</button>
                            </form>
                            <form action="<?= base_url('dashboard/users/update-role/'.$u->id) ?>" method="post">
                                <?= csrf_field() ?>
                                <input type="hidden" name="role" value="user">
                                <button title="Jadikan User Biasa" style="cursor:pointer; background:#64748b; color:white; border:none; padding:6px; border-radius:4px; font-size:10px; font-weight:bold;">USR</button>
                            </form>
                        </div>
                        <?php else: ?>
                            <small style="color: #94a3b8; font-style: italic;">Akun Anda</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>