<?= $this->extend('layout/dashboard_template') ?>

<?= $this->section('dashboard_content') ?>
<?php
$isEdit    = isset($artikel);
$actionUrl = $isEdit ? base_url('dashboard/artikel/update/' . (int) $artikel['id']) : base_url('dashboard/artikel/simpan');
$adaStatus = \App\Models\ArtikelModel::adaKolomStatus();
$statusSaatIni = old('status', $isEdit ? ($artikel['status'] ?? 'published') : 'published');
$thumbnailSaatIni = $isEdit && ! empty($artikel['thumbnail']) ? base_url('uploads/artikel/' . $artikel['thumbnail']) : '';
$kategoriDipilih = (string) old('kategori_id', $isEdit ? ($artikel['kategori_id'] ?? '') : '');
?>

<link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/42.0.0/ckeditor5.css">

<div class="page-head">
    <div>
        <a class="link-more" href="<?= base_url('dashboard/artikel') ?>">&larr; Kembali ke Kelola Artikel</a>
        <h1 style="margin-top: 8px;"><?= $isEdit ? 'Edit Artikel' : 'Tulis Artikel Baru' ?></h1>
        <p><?= $isEdit ? 'Perbarui tulisanmu, lalu simpan perubahan.' : 'Tulis dengan tenang. Kamu bisa menyimpannya sebagai draf dan menerbitkannya nanti.' ?></p>
    </div>
    <?php if ($isEdit && (($artikel['status'] ?? 'published') === 'published' || ! $adaStatus)): ?>
        <a class="btn btn--ghost" href="<?= base_url('artikel/' . esc($artikel['slug'], 'url')) ?>" target="_blank" rel="noopener">Lihat di situs &nearr;</a>
    <?php endif ?>
</div>

<?php if (empty($kategori)): ?>
    <div class="alert alert--info" role="status">
        Belum ada kategori. Artikel harus memiliki kategori, jadi buat satu dulu
        <?php if (auth()->user()->inGroup('admin', 'developer')): ?>
            di <a href="<?= base_url('dashboard/kategori/tambah') ?>"><strong>sini</strong></a>.
        <?php else: ?>
            atau minta admin untuk membuatkannya.
        <?php endif ?>
    </div>
<?php endif ?>

<form class="editor-layout" action="<?= esc($actionUrl, 'attr') ?>" method="post" enctype="multipart/form-data" data-warn-unsaved>
    <?= csrf_field() ?>

    <div class="card card--pad">
        <div class="field">
            <label class="label" for="judul">Judul artikel <span class="req">*</span></label>
            <input class="input" style="font-size: 1.15rem; font-weight: 700;" type="text" id="judul" name="judul" maxlength="200" data-counter="#hitungJudul"
                   value="<?= old('judul', $isEdit ? $artikel['judul'] : '') ?>" required placeholder="Tulis judul yang menarik…">
            <div class="flex" style="justify-content: space-between;">
                <span class="hint">Minimal 5 karakter.</span>
                <span class="counter" id="hitungJudul">0 / 200</span>
            </div>
        </div>

        <div class="field mb-0">
            <label class="label" for="editor">Isi artikel <span class="req">*</span></label>
            <textarea name="konten" id="editor" rows="16"><?= old('konten', $isEdit ? $artikel['konten'] : '') ?></textarea>
            <span class="hint">Mendukung judul, tebal, miring, daftar, kutipan, dan tautan.</span>
        </div>
    </div>

    <aside class="editor-side">
        <div class="card card--pad">
            <?php if ($adaStatus): ?>
                <div class="field">
                    <span class="label">Status</span>
                    <div class="seg" role="radiogroup" aria-label="Status artikel">
                        <label><input type="radio" name="status" value="published" <?= $statusSaatIni !== 'draft' ? 'checked' : '' ?>><span>Terbit</span></label>
                        <label><input type="radio" name="status" value="draft" <?= $statusSaatIni === 'draft' ? 'checked' : '' ?>><span>Draf</span></label>
                    </div>
                    <span class="hint">Draf hanya terlihat olehmu dan admin.</span>
                </div>
            <?php endif ?>

            <div class="field">
                <label class="label" for="kategori_id">Kategori <span class="req">*</span></label>
                <select class="select" id="kategori_id" name="kategori_id" required>
                    <option value="" disabled <?= $kategoriDipilih === '' ? 'selected' : '' ?>>Pilih kategori</option>
                    <?php foreach ($kategori ?? [] as $k): ?>
                        <option value="<?= (int) $k['id'] ?>" <?= $kategoriDipilih === (string) $k['id'] ? 'selected' : '' ?>><?= esc($k['nama_kategori']) ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="field">
                <label class="label" for="thumbnail">Gambar sampul</label>
                <div class="upload-zone">
                    <div data-preview-holder <?= $thumbnailSaatIni === '' ? 'hidden' : '' ?>>
                        <img id="previewThumb" src="<?= esc($thumbnailSaatIni, 'attr') ?>" alt="Pratinjau gambar sampul" <?= $thumbnailSaatIni === '' ? 'hidden' : '' ?>>
                    </div>
                    <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png" data-preview="#previewThumb">
                </div>
                <span class="hint"><?= $isEdit ? 'Biarkan kosong jika tidak ingin mengganti. ' : '' ?>JPG atau PNG, maksimal 2 MB.</span>
            </div>

            <div class="flex" style="flex-direction: column; gap: 10px;">
                <button type="submit" class="btn btn--primary btn--block btn--lg"><?= $isEdit ? 'Simpan Perubahan' : 'Simpan Artikel' ?></button>
                <a class="btn btn--ghost btn--block" href="<?= base_url('dashboard/artikel') ?>">Batal</a>
            </div>
        </div>
    </aside>
</form>

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
        Underline,
        Heading,
        List,
        Link,
        BlockQuote
    } from 'ckeditor5';

    ClassicEditor
        .create(document.querySelector('#editor'), {
            plugins: [Essentials, Paragraph, Bold, Italic, Underline, Heading, List, Link, BlockQuote],
            toolbar: ['heading', '|', 'bold', 'italic', 'underline', 'link', '|', 'bulletedList', 'numberedList', 'blockQuote', '|', 'undo', 'redo'],
            placeholder: 'Mulai menulis di sini…'
        })
        .then(function (editor) {
            editor.model.document.on('change:data', function () {
                if (window.RR) { window.RR.markDirty(); }
            });
        })
        .catch(function (error) {
            console.error(error);
        });
</script>
<?= $this->endSection() ?>
