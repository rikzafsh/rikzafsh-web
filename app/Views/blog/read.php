<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<article style="max-width: 800px; margin: auto; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
    
    <span style="background: <?= $art['kat_colour'] ?>; color: #fff; padding: 4px 12px; border-radius: 20px; font-size: 0.8em; font-weight: bold;"><?= $art['nama_kategori'] ?></span>
    
    <h1 style="margin: 20px 0 10px 0; line-height: 1.2; color: var(--theme-primary);"><?= $art['judul'] ?></h1>
    
    <div style="font-size: 0.9em; color: #777; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; align-items: center; gap: 12px;">
            <?php 
                $fotoPenulis = !empty($art['avatar']) ? base_url('uploads/avatars/' . $art['avatar']) : "https://ui-avatars.com/api/?name=" . urlencode($art['username']) . "&size=45&background=random&bold=true"; 
            ?>
            <a href="/profile/<?= esc($art['username']) ?>">
                <img src="<?= $fotoPenulis ?>" alt="Foto Penulis" style="border-radius: 50%; border: 2px solid #eee; width: 45px; height: 45px; object-fit: cover; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            </a>
            <div>
                <span style="display: block;">Oleh <a href="/profile/<?= esc($art['username']) ?>" style="color: #333; text-decoration: none; font-weight: bold;" onmouseover="this.style.color='var(--theme-accent)'" onmouseout="this.style.color='#333'"><?= esc($art['full_name'] ?? $art['username']) ?></a></span>
                <span style="font-size: 0.85em;"><?= date('d M Y H:i', strtotime($art['created_at'])) ?></span>
            </div>
        </div>
        
        <?php if (auth()->loggedIn() && auth()->id() == $art['penulis_id']): ?>
            <a href="/dashboard/artikel/edit/<?= $art['id'] ?>" style="color: var(--theme-accent); text-decoration: none; font-weight: bold; padding: 5px 10px; background: #f0f4f8; border-radius: 6px; display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708l-3-3zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207l6.5-6.5zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11l.178-.178z"/></svg> 
                Edit Artikel
            </a>
        <?php endif; ?>
    </div>

    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 30px; background: #fdfdfd; padding: 8px 12px; border-radius: 8px; border: 1px solid #eef0f2; width: max-content; flex-wrap: wrap;">
        
        <div style="background: #f1f3f5; color: #666; padding: 6px 14px; border-radius: 4px; font-weight: bold; font-size: 0.85em; display: flex; align-items: center; gap: 6px;">
            <svg width="15" height="15" fill="currentColor" viewBox="0 0 16 16"><path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0z"/><path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8zm8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7z"/></svg>
            <?= number_format($art['views']) ?> Views
        </div>

        <form action="/artikel/reaksi" method="post" style="margin: 0;">
            <input type="hidden" name="artikel_id" value="<?= $art['id'] ?>">
            <input type="hidden" name="jenis" value="like">
            <button type="submit" style="background: <?= $art['reaksi_user'] === 'like' ? 'var(--theme-accent)' : '#eee' ?>; color: <?= $art['reaksi_user'] === 'like' ? '#fff' : '#333' ?>; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.85em; display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.075.265.306 1.039-.55 1.7-.015.012-.03.024-.045.036.03.111.06.223.088.337.072.296.223 1.036-.5 1.722-.058.055-.116.108-.173.16.03.123.056.248.077.375.04.225.12 1.12-.5 1.745-.045.045-.09.088-.135.129C13.518 15.65 12.6 16 11.5 16H8c-1.5 0-3-1.5-3-3V5.5l1.956-3.755zM3 5.5h2v10H3v-10z"/></svg>
                Like (<?= $art['total_like'] ?>)
            </button>
        </form>

        <form action="/artikel/reaksi" method="post" style="margin: 0;">
            <input type="hidden" name="artikel_id" value="<?= $art['id'] ?>">
            <input type="hidden" name="jenis" value="dislike">
            <button type="submit" style="background: <?= $art['reaksi_user'] === 'dislike' ? 'var(--dangerRed)' : '#eee' ?>; color: <?= $art['reaksi_user'] === 'dislike' ? '#fff' : '#333' ?>; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.85em; display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M6.956 14.255c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.075-.266.306-1.04-.55-1.7-.015-.012-.03-.024-.045-.036.03-.111.06-.223.088-.337.072-.296.223-1.036-.5-1.721-.058-.055-.116-.109-.173-.16.03-.123.056-.249.077-.375.04-.224.12-1.12-.5-1.744C13.518.35 12.6 0 11.5 0H8c-1.5 0-3 1.5-3 3v7.5l1.956 3.755zM3 10.5h2V.5H3v10z"/></svg>
                Dislike (<?= $art['total_dislike'] ?>)
            </button>
        </form>

        <form action="/artikel/simpan" method="post" style="margin: 0;">
            <input type="hidden" name="artikel_id" value="<?= $art['id'] ?>">
            <button type="submit" style="background: <?= $art['is_saved'] > 0 ? '#ff9800' : '#eee' ?>; color: <?= $art['is_saved'] > 0 ? '#fff' : '#333' ?>; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 0.85em; display: flex; align-items: center; gap: 6px;" title="<?= $art['is_saved'] > 0 ? 'Batal Simpan Artikel' : 'Simpan Artikel ini' ?>">
                <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2z"/></svg>
                <?= $art['is_saved'] > 0 ? 'Tersimpan' : 'Simpan' ?> (<?= $art['total_simpan'] ?>)
            </button>
        </form>
    </div>

    <?php if ($art['thumbnail']): ?>
        <img src="<?= base_url('uploads/artikel/' . $art['thumbnail']) ?>" style="width: 100%; border-radius: 8px; margin-bottom: 30px;">
    <?php endif; ?>

    <div class="konten-artikel" style="line-height: 1.8; color: #333; font-size: 1.1em; margin-bottom: 40px;">
        <?= $art['konten'] ?>
    </div>

    <hr style="margin: 40px 0; border: 0; border-top: 1px solid #eee;">

    <h3>Kolom Diskusi (<?= count($komentar) ?>)</h3>
    
    <?php if (auth()->loggedIn()): ?>
        <form action="/artikel/komentar" method="post" style="margin-bottom: 40px;">
            <input type="hidden" name="artikel_id" value="<?= $art['id'] ?>">
            <textarea name="isi_komentar" placeholder="Tulis opini atau pertanyaanmu disini..." required style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; height: 90px; box-sizing: border-box; font-family: inherit;"></textarea>
            <button type="submit" style="margin-top: 10px; padding: 10px 25px; background: var(--theme-button); color: #fff; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Kirim Komentar</button>
        </form>
    <?php else: ?>
        <p style="background: #f9f9f9; padding: 15px; border-radius: 8px; text-align: center; font-size: 0.9em; border: 1px dashed #ddd;">
            Silakan <a href="/login" style="color: var(--theme-button); font-weight: bold;">Login</a> untuk ikut menulis komentar.
        </p>
    <?php endif; ?>

    <?php foreach ($komentar as $k): ?>
        <div style="margin-bottom: 30px; border-bottom: 1px solid #f4f4f4; padding-bottom: 20px;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                <?php 
                    $fotoKomentar = !empty($k['avatar']) ? base_url('uploads/avatars/' . $k['avatar']) : "https://ui-avatars.com/api/?name=" . urlencode($k['username']) . "&size=36&background=random"; 
                ?>
                <a href="/profile/<?= esc($k['username']) ?>">
                    <img src="<?= $fotoKomentar ?>" style="border-radius: 50%; width: 36px; height: 36px; object-fit: cover;" alt="Avatar Pengomentar">
                </a>
                <a href="/profile/<?= esc($k['username']) ?>" style="color: var(--theme-primary); font-size: 0.95em; font-weight: bold; text-decoration: none;"><?= esc($k['full_name'] ?? $k['username']) ?></a>
                <small style="color: #999; font-size: 0.8em;">• <?= date('d M Y H:i', strtotime($k['created_at'])) ?></small>
            </div>
            
            <p style="margin: 0 0 12px 46px; color: #333; line-height: 1.5;"><?= esc($k['isi_komentar']) ?></p>
            
            <div style="margin-left: 46px; display: flex; align-items: center; gap: 12px;">
                <form action="/komentar/reaksi" method="post">
                    <input type="hidden" name="komentar_id" value="<?= $k['id'] ?>">
                    <input type="hidden" name="jenis" value="like">
                    <button type="submit" style="background: none; border: none; color: <?= $k['reaksi_user'] === 'like' ? 'var(--theme-accent)' : '#777' ?>; cursor: pointer; font-size: 0.85em; font-weight: bold; display: flex; align-items: center; gap: 4px; padding: 0;">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.075.265.306 1.039-.55 1.7-.015.012-.03.024-.045.036.03.111.06.223.088.337.072.296.223 1.036-.5 1.722-.058.055-.116.108-.173.16.03.123.056.248.077.375.04.225.12 1.12-.5 1.745-.045.045-.09.088-.135.129C13.518 15.65 12.6 16 11.5 16H8c-1.5 0-3-1.5-3-3V5.5l1.956-3.755zM3 5.5h2v10H3v-10z"/></svg> 
                        <?= $k['total_like'] ?>
                    </button>
                </form>

                <form action="/komentar/reaksi" method="post">
                    <input type="hidden" name="komentar_id" value="<?= $k['id'] ?>">
                    <input type="hidden" name="jenis" value="dislike">
                    <button type="submit" style="background: none; border: none; color: <?= $k['reaksi_user'] === 'dislike' ? 'var(--dangerRed)' : '#777' ?>; cursor: pointer; font-size: 0.85em; font-weight: bold; margin-right: 10px; display: flex; align-items: center; gap: 4px; padding: 0;">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 16 16"><path d="M6.956 14.255c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.075-.266.306-1.04-.55-1.7-.015-.012-.03-.024-.045-.036.03-.111.06-.223.088-.337.072-.296.223-1.036-.5-1.721-.058-.055-.116-.109-.173-.16.03-.123.056-.249.077-.375.04-.224.12-1.12-.5-1.744C13.518.35 12.6 0 11.5 0H8c-1.5 0-3 1.5-3 3v7.5l1.956 3.755zM3 10.5h2V.5H3v10z"/></svg> 
                        <?= $k['total_dislike'] ?>
                    </button>
                </form>

                <button onclick="showReplyForm(<?= $k['id'] ?>)" style="background: #f0f2f5; border: none; padding: 4px 12px; border-radius: 12px; color: #555; font-weight: 600; cursor: pointer; font-size: 0.8em;">Balas</button>
            </div>

            <?php if (isset($balasan[$k['id']])): ?>
                <div style="margin-left: 46px; border-left: 2px solid #eee; padding-left: 20px; margin-top: 15px;">
                    <?php foreach ($balasan[$k['id']] as $b): ?>
                        <div style="margin-bottom: 15px; background: #fafafa; padding: 12px; border-radius: 6px;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <?php 
                                    $fotoBalasan = !empty($b['avatar']) ? base_url('uploads/avatars/' . $b['avatar']) : "https://ui-avatars.com/api/?name=" . urlencode($b['username']) . "&size=28&background=random"; 
                                ?>
                                <a href="/profile/<?= esc($b['username']) ?>">
                                    <img src="<?= $fotoBalasan ?>" style="border-radius: 50%; width: 28px; height: 28px; object-fit: cover;" alt="Avatar Pembalas">
                                </a>
                                <a href="/profile/<?= esc($b['username']) ?>" style="font-size: 0.85em; color: var(--theme-primary); font-weight: bold; text-decoration: none;"><?= esc($b['full_name'] ?? $b['username']) ?></a>
                                <small style="color: #999; font-size: 0.75em;">• <?= date('d M Y H:i', strtotime($b['created_at'])) ?></small>
                            </div>
                            
                            <p style="margin: 0 0 10px 36px; font-size: 0.95em; color: #444;"><?= esc($b['isi_komentar']) ?></p>
                            
                            <div style="display: flex; gap: 12px; margin-left: 36px;">
                                <form action="/komentar/reaksi" method="post">
                                    <input type="hidden" name="komentar_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="jenis" value="like">
                                    <button type="submit" style="background: none; border: none; color: <?= $b['reaksi_user'] === 'like' ? 'var(--theme-accent)' : '#777' ?>; cursor: pointer; font-size: 0.8em; font-weight: bold; display: flex; align-items: center; gap: 4px; padding: 0;">
                                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M6.956 1.745C7.021.81 7.908.087 8.864.325l.261.066c.463.116.874.456 1.012.965.22.816.533 2.511.062 4.51a9.84 9.84 0 0 1 .443-.051c.713-.065 1.669-.072 2.516.21.518.173.994.681 1.2 1.273.184.532.16 1.162-.234 1.733.058.119.103.242.138.363.075.265.306 1.039-.55 1.7-.015.012-.03.024-.045.036.03.111.06.223.088.337.072.296.223 1.036-.5 1.722-.058.055-.116.108-.173.16.03.123.056.248.077.375.04.225.12 1.12-.5 1.745-.045.045-.09.088-.135.129C13.518 15.65 12.6 16 11.5 16H8c-1.5 0-3-1.5-3-3V5.5l1.956-3.755zM3 5.5h2v10H3v-10z"/></svg> 
                                        <?= $b['total_like'] ?>
                                    </button>
                                </form>
                                <form action="/komentar/reaksi" method="post">
                                    <input type="hidden" name="komentar_id" value="<?= $b['id'] ?>">
                                    <input type="hidden" name="jenis" value="dislike">
                                    <button type="submit" style="background: none; border: none; color: <?= $b['reaksi_user'] === 'dislike' ? 'var(--dangerRed)' : '#777' ?>; cursor: pointer; font-size: 0.8em; font-weight: bold; display: flex; align-items: center; gap: 4px; padding: 0;">
                                        <svg width="13" height="13" fill="currentColor" viewBox="0 0 16 16"><path d="M6.956 14.255c.065.936.952 1.659 1.908 1.42l.261-.065a1.378 1.378 0 0 0 1.012-.965c.22-.816.533-2.512.062-4.51.136.02.285.037.443.051.713.065 1.669.071 2.516-.211.518-.173.994-.68 1.2-1.272a1.896 1.896 0 0 0-.234-1.734c.058-.118.103-.242.138-.362.075-.266.306-1.04-.55-1.7-.015-.012-.03-.024-.045-.036.03-.111.06-.223.088-.337.072-.296.223-1.036-.5-1.721-.058-.055-.116-.109-.173-.16.03-.123.056-.249.077-.375.04-.224.12-1.12-.5-1.744C13.518.35 12.6 0 11.5 0H8c-1.5 0-3 1.5-3 3v7.5l1.956 3.755zM3 10.5h2V.5H3v10z"/></svg> 
                                        <?= $b['total_dislike'] ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div id="reply-form-<?= $k['id'] ?>" style="display: none; margin-left: 46px; margin-top: 15px;">
                <form action="/artikel/komentar" method="post">
                    <input type="hidden" name="artikel_id" value="<?= $art['id'] ?>">
                    <input type="hidden" name="parent_id" value="<?= $k['id'] ?>">
                    <textarea name="isi_komentar" placeholder="Tulis balasanmu..." required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ddd; font-family: inherit; font-size: 0.9em; height: 60px; box-sizing: border-box;"></textarea>
                    <div style="margin-top: 5px; display: flex; gap: 5px;">
                        <button type="submit" style="padding: 5px 15px; background: var(--theme-accent); color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 0.8em; font-weight: bold;">Kirim</button>
                        <button type="button" onclick="hideReplyForm(<?= $k['id'] ?>)" style="padding: 5px 10px; background: #ddd; border: none; border-radius: 4px; color: #333; cursor: pointer; font-size: 0.8em;">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>

</article>

<script>
function showReplyForm(id) {
    document.getElementById('reply-form-' + id).style.display = 'block';
}
function hideReplyForm(id) {
    document.getElementById('reply-form-' + id).style.display = 'none';
}
</script>
<?= $this->endSection() ?>