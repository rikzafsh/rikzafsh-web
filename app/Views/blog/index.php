<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>

<h1 style="color: var(--theme-primary); margin-bottom: 20px;">Penjelajah Artikel</h1>

<form action="/artikel" method="get" style="background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; flex-wrap: wrap; gap: 15px; align-items: flex-end;">
    
    <div style="flex: 2; min-width: 200px;">
        <label style="display:block; font-size:0.85em; font-weight:bold; margin-bottom:5px;">Kata Kunci</label>
        <input type="text" name="cari" value="<?= esc($keyword) ?>" placeholder="Cari judul atau isi artikel..." style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box;">
    </div>

    <div style="flex: 1; min-width: 150px;">
        <label style="display:block; font-size:0.85em; font-weight:bold; margin-bottom:5px;">Kategori</label>
        <select name="kategori" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="">Semua Kategori</option>
            <?php foreach ($semua_kategori as $kat): ?>
                <option value="<?= $kat['slug'] ?>" <?= $kategori_aktif == $kat['slug'] ? 'selected' : '' ?>><?= esc($kat['nama_kategori']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="flex: 1; min-width: 150px;">
        <label style="display:block; font-size:0.85em; font-weight:bold; margin-bottom:5px;">Urutkan</label>
        <select name="sort" style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
            <option value="terbaru" <?= $sort_aktif == 'terbaru' ? 'selected' : '' ?>>Terbaru</option>
            <option value="terlama" <?= $sort_aktif == 'terlama' ? 'selected' : '' ?>>Terlama</option>
        </select>
    </div>

    <?php if (!empty($penulis_aktif)): ?>
        <input type="hidden" name="penulis" value="<?= esc($penulis_aktif) ?>">
    <?php endif; ?>

    <div style="display: flex; gap: 10px;">
        <button type="submit" style="padding: 9px 20px; background: var(--theme-button); color: #fff; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">Filter</button>
        
        <?php if (!empty($keyword) || !empty($kategori_aktif) || !empty($penulis_aktif)): ?>
            <a href="/artikel" style="padding: 8px 15px; background: #e0e0e0; color: #333; border-radius: 4px; text-decoration: none; font-size: 0.9em; font-weight: bold; display: inline-block;">Reset</a>
        <?php endif; ?>
    </div>
</form>

<?php if (!empty($nama_penulis_aktif)): ?>
    <p style="background: var(--theme-bg-sidebar); padding: 10px 15px; border-radius: 4px; color: var(--theme-primary); font-weight: 500; margin-bottom: 20px;">
        Menampilkan artikel yang ditulis oleh: <strong><?= esc($nama_penulis_aktif) ?></strong>
    </p>
<?php endif; ?>


<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
    <?php if (!empty($daftar_artikel) && is_array($daftar_artikel)): ?>
        <?php foreach ($daftar_artikel as $art): ?>
            <article style="background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 3px 10px rgba(0,0,0,0.05); display: flex; flex-direction: column;">
                
                <a href="/artikel/<?= $art['slug'] ?>" style="display: block; overflow: hidden;">
                    <?php if (!empty($art['thumbnail'])): ?>
                        <img src="<?= base_url('uploads/artikel/' . $art['thumbnail']) ?>" alt="Thumbnail Artikel" style="width: 100%; height: 180px; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">

                    <?php elseif (!empty($art['cover_image'])): ?>
                        <img src="<?= base_url('uploads/kategori/' . $art['cover_image']) ?>" alt="Cover Kategori" style="width: 100%; height: 180px; object-fit: cover; transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">

                    <?php else: ?>
                        <div style="width: 100%; height: 180px; background: #ddd; display: flex; align-items: center; justify-content: center; color: #888; font-weight: bold;">No Image</div>
                    <?php endif; ?>
                </a>

                <div style="padding: 20px; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div>
                        <a href="/artikel?kategori=<?= $art['kat_slug'] ?>" style="background: <?= esc($art['kat_colour']) ?>; color: #fff; font-size: 0.75em; padding: 4px 10px; border-radius: 20px; font-weight: bold; text-transform: uppercase; text-decoration: none; display: inline-block;">
                            <?= esc($art['nama_kategori']) ?>
                        </a>
                        
                        <h3 style="margin: 15px 0 10px 0; font-size: 1.25em;">
                            <a href="/artikel/<?= $art['slug'] ?>" style="color: var(--theme-text); text-decoration: none; transition: color 0.2s;" onmouseover="this.style.color='var(--theme-accent)'" onmouseout="this.style.color='var(--theme-text)'">
                                <?= esc($art['judul']) ?>
                            </a>
                        </h3>
                        
                        <p style="color: #666; font-size: 0.9em; line-height: 1.5; margin-bottom: 15px;">
                            <?= strip_tags(substr($art['konten'], 0, 120)) ?>...
                        </p>
                    </div>

                    <div style="border-top: 1px solid #eee; padding-top: 10px; font-size: 0.8em; color: #777; display: flex; justify-content: space-between; align-items: center;">
                        <span>Oleh: 
                            <a href="/artikel?penulis=<?= $art['username'] ?>" style="color: var(--theme-button); font-weight: bold; text-decoration: none;">
                                <?= esc(!empty($art['full_name']) ? $art['full_name'] : $art['username']) ?>
                            </a>
                        </span>
                        <span><?= date('d M Y', strtotime($art['created_at'])) ?></span>
                    </div>
                </div>

            </article>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 40px; color: #888;">
            <h3>Artikel tidak ditemukan.</h3>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>