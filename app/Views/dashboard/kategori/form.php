<?= $this->extend('layout/dashboard_template') ?>

<?= $this->section('dashboard_content') ?>

<?php 
$isEdit = isset($kategori);
$actionUrl = $isEdit ? "/dashboard/kategori/update/{$kategori['id']}" : "/dashboard/kategori/simpan";
?>

<div style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); padding: 30px; max-width: 700px; margin: 0 auto;">
    
    <div style="margin-bottom: 25px;">
        <a href="/dashboard/artikel" style="color: #4e73df; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px;">
            ← Kembali ke Kelola Artikel
        </a>
        <h2 style="margin: 15px 0 0 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">
            <?= $isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru' ?>
        </h2>
    </div>

    <form action="<?= $actionUrl ?>" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
        <?= csrf_field() ?>

        <div>
            <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Nama Kategori <span style="color: red;">*</span></label>
            <input type="text" name="nama_kategori" value="<?= old('nama_kategori', $isEdit ? $kategori['nama_kategori'] : '') ?>" required placeholder="Contoh: Pemrograman, Gaya Hidup" style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box;">
        </div>

        <div>
            <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Warna Label Kategori <span style="color: red;">*</span></label>
            <div style="display: flex; gap: 10px; align-items: center;">
                <input type="color" name="colour" value="<?= old('colour', $isEdit ? $kategori['colour'] : '#4e73df') ?>" style="width: 50px; height: 40px; padding: 0; border: 1px solid #cbd5e1; border-radius: 6px; cursor: pointer;">
                <span style="font-size: 0.85rem; color: #718096;">Pilih warna penanda untuk kategori ini</span>
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Cover Image Kategori</label>
            <?php if ($isEdit && !empty($kategori['cover_image'])): ?>
                <div style="margin-bottom: 10px;">
                    <p style="margin: 0 0 5px 0; font-size: 0.8rem; color: #718096;">Cover saat ini:</p>
                    <img src="/uploads/kategori/<?= esc($kategori['cover_image']) ?>" style="width: 150px; height: 90px; object-fit: cover; border-radius: 6px; border: 1px solid #cbd5e1;">
                </div>
            <?php endif; ?>
            <input type="file" name="cover_image" accept="image/*" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #f8fafc; box-sizing: border-box;">
            <small style="display: block; color: #a0aec0; margin-top: 5px;">Format: JPG, JPEG, PNG, WEBP. Maksimal 2MB.</small>
        </div>

        <div>
            <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Deskripsi Kategori</label>
            <textarea name="deskripsi" rows="4" placeholder="Tulis penjelasan singkat mengenai kategori ini..." style="width: 100%; padding: 12px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; font-family: inherit; resize: vertical; box-sizing: border-box;"><?= old('deskripsi', $isEdit ? $kategori['deskripsi'] : '') ?></textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px;">
            <a href="/dashboard/artikel" style="background: #e2e8f0; color: #4a5568; padding: 10px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 0.9rem;">Batal</a>
            <button type="submit" style="background: #28a745; color: white; padding: 10px 25px; border: none; border-radius: 6px; font-weight: bold; font-size: 0.9rem; cursor: pointer;">
                <?= $isEdit ? 'Simpan Perubahan' : 'Simpan Kategori' ?>
            </button>
        </div>
    </form>
</div>

<?= $this->endSection() ?>