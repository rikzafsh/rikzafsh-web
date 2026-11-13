<?= $this->extend('layout/dashboard_template') ?>

<?= $this->section('dashboard_content') ?>
<div style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); padding: 30px;">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">Kelola Proyek</h2>
    </div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 6px; margin-bottom: 20px; font-weight: 600;">
            <?= session()->getFlashdata('message'); ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 20px; border-bottom: 2px solid #edf2f7; margin-bottom: 25px; padding-bottom: 10px;">
        <button id="btn-tab-pustaka" onclick="switchTab('tab-pustaka', this)" class="tab-btn" style="background: none; border: none; font-size: 1rem; font-weight: 700; color: #4e73df; border-bottom: 3px solid #4e73df; padding-bottom: 8px; cursor: pointer;">Pustaka Karya</button>
        <button id="btn-tab-web" onclick="switchTab('tab-web', this)" class="tab-btn" style="background: none; border: none; font-size: 1rem; font-weight: 600; color: #718096; padding-bottom: 8px; cursor: not-allowed;">Web Apps (Soon)</button>
        <button id="btn-tab-desain" onclick="switchTab('tab-desain', this)" class="tab-btn" style="background: none; border: none; font-size: 1rem; font-weight: 600; color: #718096; padding-bottom: 8px; cursor: not-allowed;">Desain Grafis (Soon)</button>
    </div>

    <div id="tab-pustaka" class="tab-content" style="display: block;">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
            <div style="color: #4a5568; font-weight: 600;">
                Total Edisi: <span style="background: #edf2f7; padding: 2px 8px; border-radius: 4px; color: #2d3748;"><?= count($majalah) ?></span>
            </div>
            <button onclick="toggleModal('addModal')" style="background: #3a43e0; color: white; padding: 10px 20px; border: none; border-radius: 6px; font-weight: bold; font-size: 0.9rem; cursor: pointer;">+ Tambah Edisi</button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.95rem;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #edf2f7;">
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700;">Cover</th>
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700;">Nomor Edisi</th>
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700;">Tema</th>
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700;">Periode</th>
                        <th style="padding: 14px 12px; color: #4a5568; font-weight: 700;">Warna</th>
                        <th style="padding: 14px 12px; text-align: center; color: #4a5568; font-weight: 700;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($majalah)): ?>
                        <tr><td colspan="6" style="padding: 40px; text-align: center; color: #a0aec0;">Belum ada data edisi majalah.</td></tr>
                    <?php else: ?>
                        <?php foreach ($majalah as $mjl): ?>
                        <tr style="border-bottom: 1px solid #edf2f7;">
                            <td style="padding: 16px 12px;">
                                <img src="<?= base_url('uploads/covers/' . ($mjl['cover_img'] ?? '')) ?>" style="width: 45px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid #edf2f7;" onerror="this.src='https://placehold.co/45x60?text=No+Img'">
                            </td>
                            <td style="padding: 16px 12px; color: #2d3748; font-weight: bold;">
                                <?= esc($mjl['edisi_romawi']) ?>
                                <div style="font-size: 0.8rem; color: #a0aec0; margin-top: 2px; font-weight: normal;"><?= esc($mjl['codename']) ?></div>
                            </td>
                            <td style="padding: 16px 12px; color: #4a5568; max-width: 200px; line-height: 1.4;"><?= esc($mjl['tema']) ?></td>
                            <td style="padding: 16px 12px; color: #4a5568;"><?= esc($mjl['periode_teks']) ?></td>
                            <td style="padding: 16px 12px;">
                                <div style="display: flex; align-items: center; gap: 8px;">
                                    <?php $hexColor = safe_color($mjl['warna'] ?? null, '#29AAFE'); ?>
                                    <div style="width: 24px; height: 24px; border-radius: 50%; background-color: <?= $hexColor ?>; border: 1px solid #cbd5e1;"></div>
                                    <span style="font-family: monospace; font-size: 0.85rem; color: #4a5568; background: #edf2f7; padding: 2px 6px; border-radius: 4px;"><?= strtoupper($hexColor) ?></span>
                                </div>
                            </td>
                            <td style="padding: 16px 12px; text-align: center; white-space: nowrap;">
                                <button onclick='openEditModal(<?= esc(json_encode($mjl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT), 'attr') ?>)' style="display: inline-flex; border: none; background: #f59e0b; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem; margin-right: 6px; cursor: pointer;">Edit</button>
                                <form action="<?= base_url('dashboard/projects/pustaka/hapus/' . $mjl['id']) ?>" method="post" style="display: inline; margin: 0;" onsubmit="return confirm('Hapus edisi majalah ini?')"><?= csrf_field() ?><button type="submit" style="display: inline-flex; background: #ef4444; color: white; padding: 6px 14px; text-decoration: none; border-radius: 4px; font-weight: bold; font-size: 0.85rem; border: none; cursor: pointer; font-family: inherit;">Hapus</button></form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="tab-web" class="tab-content" style="display: none; padding: 40px; text-align: center; color: #a0aec0;">Modul Web Apps masih dalam pengembangan.</div>
    <div id="tab-desain" class="tab-content" style="display: none; padding: 40px; text-align: center; color: #a0aec0;">Modul Desain Grafis masih dalam pengembangan.</div>

</div>


<div id="addModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; width: 100%; max-width: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; color: #2d3748;">Tambah Edisi Baru</h3>
        <form action="<?= base_url('dashboard/projects/pustaka/simpan') ?>" method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
            <?= csrf_field() ?>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Codename</label><input type="text" name="codename" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Edisi (Angka)</label><input type="number" name="edisi" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
            </div>
            <div><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Tema</label><input type="text" name="tema" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Tanggal Rilis</label><input type="date" name="tanggal_rilis" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Warna Label</label><input type="color" name="warna" value="#29AAFE" style="width: 100%; height: 35px; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer;"></div>
            </div>
            <div><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Link Drive</label><input type="url" name="link" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
            <div><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Gambar Cover</label><input type="file" name="cover_img" required style="width: 100%; padding: 4px;"></div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="toggleModal('addModal')" style="background: #edf2f7; color: #4a5568; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">Batal</button>
                <button type="submit" style="background: #3a43e0; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" style="position: fixed; inset: 0; background: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center; z-index: 999;">
    <div style="background: #fff; padding: 30px; border-radius: 8px; width: 100%; max-width: 500px; box-shadow: 0 4px 15px rgba(0,0,0,0.2);">
        <h3 style="margin-top: 0; color: #2d3748;">Edit Edisi</h3>
        <form id="editForm" action="" method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 12px; margin-top: 15px;">
            <?= csrf_field() ?>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Codename</label><input type="text" name="codename" id="edit_codename" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Edisi (Angka)</label><input type="number" name="edisi" id="edit_edisi" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
            </div>
            <div><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Tema</label><input type="text" name="tema" id="edit_tema" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
            <div style="display: flex; gap: 10px;">
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Tanggal Rilis</label><input type="date" name="tanggal_rilis" id="edit_tanggal_rilis" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
                <div style="flex: 1;"><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Warna Label</label><input type="color" name="warna" id="edit_warna" style="width: 100%; height: 35px; border: 1px solid #cbd5e1; border-radius: 4px; cursor: pointer;"></div>
            </div>
            <div><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Link Drive</label><input type="url" name="link" id="edit_link" required style="width: 100%; padding: 8px; border: 1px solid #cbd5e1; border-radius: 4px; box-sizing: border-box;"></div>
            <div><label style="font-size: 0.85rem; color: #4a5568; font-weight: 600;">Ganti Cover (Kosongkan jika tidak ubah)</label><input type="file" name="cover_img" style="width: 100%; padding: 4px;"></div>
            
            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 10px;">
                <button type="button" onclick="toggleModal('editModal')" style="background: #edf2f7; color: #4a5568; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">Batal</button>
                <button type="submit" style="background: #f59e0b; color: #fff; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; font-weight: bold;">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tab Controller Script
    document.addEventListener("DOMContentLoaded", function() {
        let activeTab = localStorage.getItem('activeProjectTab') || 'tab-pustaka';
        let activeBtn = document.getElementById('btn-' + activeTab);
        if (activeBtn && !activeBtn.disabled) switchTab(activeTab, activeBtn);
    });

    function switchTab(tabId, element) {
        if(element.hasAttribute('disabled')) return;

        document.querySelectorAll('.tab-content').forEach(tab => tab.style.display = 'none');
        document.getElementById(tabId).style.display = 'block';

        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.style.color = '#718096';
            btn.style.borderBottom = 'none';
            btn.style.fontWeight = '600';
        });

        element.style.color = '#4e73df';
        element.style.borderBottom = '3px solid #4e73df';
        element.style.fontWeight = '700';

        localStorage.setItem('activeProjectTab', tabId);
    }

    // Modal Script
    function toggleModal(id) {
        const modal = document.getElementById(id);
        modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
    }

    function openEditModal(data) {
        document.getElementById('editForm').action = "<?= base_url('dashboard/projects/pustaka/update') ?>/" + data.id;
        document.getElementById('edit_codename').value = data.codename;
        document.getElementById('edit_edisi').value = data.edisi;
        document.getElementById('edit_tema').value = data.tema;
        document.getElementById('edit_tanggal_rilis').value = data.tanggal_rilis;
        document.getElementById('edit_warna').value = data.warna || '#29AAFE';
        document.getElementById('edit_link').value = data.link;
        toggleModal('editModal');
    }
</script>

<?= $this->endSection() ?>