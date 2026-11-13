<?= $this->extend('layout/template') ?>
<?= $this->section('content') ?>

<?php 
// Cek apakah halaman ini memuat data artikel lama (Mode Edit) atau tidak (Mode Tambah)
$isEdit = isset($artikel);
$actionUrl = $isEdit ? "/dashboard/artikel/update/{$artikel['id']}" : "/dashboard/artikel/simpan";
?>

<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.css">

<style>
    /* Styling khusus editor agar lebih leluasa saat mengetik */
    .ck-editor__editable_inline { min-height: 400px; font-family: inherit; font-size: 1rem; }
    .ck.ck-editor { width: 100%; border-radius: 6px; overflow: hidden; }
</style>

<div style="background: #ffffff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.03); padding: 30px; max-width: 900px; margin: 0 auto;">
    
    <div style="margin-bottom: 25px;">
        <a href="/dashboard/artikel" style="color: #4e73df; text-decoration: none; font-weight: 600; font-size: 0.9rem; display: inline-flex; align-items: center; gap: 5px;">
            ← Kembali ke Kelola Artikel
        </a>
        <h2 style="margin: 15px 0 0 0; color: #2d3748; font-size: 1.5rem; font-weight: 700;">
            <?= $isEdit ? 'Edit Artikel' : 'Tulis Artikel Baru' ?>
        </h2>
    </div>

    <?php if (session()->getFlashdata('errors')) : ?>
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px;">
            <ul style="margin: 0; padding-left: 20px; font-size: 0.9rem;">
            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= $actionUrl ?>" method="post" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 20px;">
        <?= csrf_field() ?>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap;">
            <div style="flex: 2; min-width: 300px;">
                <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Judul Artikel <span style="color: red;">*</span></label>
                <input type="text" name="judul" value="<?= old('judul', $isEdit ? $artikel['judul'] : '') ?>" required placeholder="Masukkan judul artikel yang menarik..." style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; box-sizing: border-box;">
            </div>

            <div style="flex: 1; min-width: 200px;">
                <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Kategori <span style="color: red;">*</span></label>
                <select name="kategori_id" required style="width: 100%; padding: 10px 14px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.95rem; background: #fff; box-sizing: border-box; cursor: pointer;">
                    <option value="" disabled <?= old('kategori_id', $isEdit ? $artikel['kategori_id'] : '') == '' ? 'selected' : '' ?>>Pilih Kategori</option>
                    
                    <?php if (isset($kategori)): ?>
                        <?php foreach ($kategori as $k): ?>
                            <?php $selected = (old('kategori_id', $isEdit ? ($artikel['kategori_id'] ?? '') : '') == $k['id']) ? 'selected' : ''; ?>
                            <option value="<?= $k['id'] ?>" <?= $selected ?>><?= esc($k['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="1">Umum</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <div>
            <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Gambar Sampul (Thumbnail)</label>
            
            <?php if ($isEdit && !empty($artikel['thumbnail'])): ?>
                <div style="margin-bottom: 10px; background: #f8fafc; padding: 10px; border-radius: 6px; border: 1px solid #e2e8f0; display: inline-block;">
                    <p style="margin: 0 0 8px 0; font-size: 0.8rem; color: #718096;">Gambar saat ini:</p>
                    <img src="/uploads/artikel/<?= esc($artikel['thumbnail']) ?>" alt="Thumbnail" style="max-width: 200px; height: auto; border-radius: 4px; display: block;">
                </div>
            <?php endif; ?>
            
            <input type="file" name="thumbnail" accept="image/*" style="width: 100%; padding: 8px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 0.9rem; background: #f8fafc; box-sizing: border-box;">
            <small style="display: block; color: #a0aec0; margin-top: 5px;">Biarkan kosong jika tidak ingin mengubah gambar sampul. (Maksimal 2MB)</small>
        </div>

        <div>
            <label style="display: block; font-weight: 700; color: #4a5568; margin-bottom: 8px; font-size: 0.9rem;">Konten Artikel <span style="color: red;">*</span></label>
            <textarea name="konten" id="editor"><?= old('konten', $isEdit ? $artikel['konten'] : '') ?></textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 10px; border-top: 1px solid #edf2f7; padding-top: 20px;">
            <a href="/dashboard/artikel" style="background: #e2e8f0; color: #4a5568; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold; font-size: 0.95rem;">Batal</a>
            <button type="submit" style="background: #009339; color: white; padding: 12px 25px; border: none; border-radius: 6px; font-weight: bold; font-size: 0.95rem; cursor: pointer;">
                <?= $isEdit ? 'Simpan Perubahan' : 'Publikasikan Artikel' ?>
            </button>
        </div>

    </form>
</div>

<script type="importmap">
    {
        "imports": {
            "ckeditor5": "https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.js",
            "ckeditor5/": "https://cdn.ckeditor.com/ckeditor5/42.0.0/"
        }
    }
</script>

<script type="module">
    import {
        ClassicEditor,
        Essentials,
        Paragraph,
        Bold,
        Italic,
        Heading,
        List,
        Link
    } from 'ckeditor5';

    ClassicEditor
        .create( document.querySelector( '#editor' ), {
            plugins: [ Essentials, Paragraph, Bold, Italic, Heading, List, Link ],
            toolbar: [ 'heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo' ]
        } )
        .catch( error => {
            console.error( error );
        } );
</script>

<?= $this->endSection() ?>