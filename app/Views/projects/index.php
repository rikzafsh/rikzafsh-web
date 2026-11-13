<?= $this->extend('layout/template') ?> <?= $this->section('content') ?>
<div style="max-width: 1200px; margin: 40px auto; padding: 0 20px; font-family: sans-serif;">
    
    <h2 style="text-align: center; margin-bottom: 40px; color: #333;">📚 Pustaka Karya</h2>

    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 30px;">
        
        <?php foreach($majalah as $mjl): ?>
        <div style="border: 1px solid #eef2f5; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05); background: #fff; display: flex; flex-direction: column;">
            
            <img src="<?= base_url('uploads/covers/' . ($mjl['cover_img'] ?? '')) ?>" alt="Cover" style="width: 100%; height: 380px; object-fit: cover; border-bottom: 4px solid <?= safe_color($mjl['warna'] ?? null, '#29AAFE') ?>;" onerror="this.src='https://placehold.co/300x400?text=No+Cover'">

            <div style="padding: 20px; flex-grow: 1; display: flex; flex-direction: column;">
                
                <div style="color: <?= safe_color($mjl['warna'] ?? null, '#29AAFE') ?>; font-weight: 800; font-size: 0.85rem; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 8px;">
                    <?= esc($mjl['edisi_romawi']) ?>. <?= esc($mjl['periode_otomatis']) ?>
                </div>
                
                <h3 style="margin: 0 0 5px 0; font-size: 1.2rem; color: #2d3748;">
                    <?= esc($mjl['tema']) ?>
                </h3>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px; border-top: 1px solid #edf2f7; padding-top: 15px;">
                    <span style="font-size: 0.8rem; color: #a0aec0;">
                        Rilis: <?= date('d M Y', strtotime($mjl['tanggal_rilis'])) ?>
                    </span>
                    
                    <a href="<?= esc(safe_url($mjl['link'] ?? '')) ?>" target="_blank" style="background: <?= safe_color($mjl['warna'] ?? null, '#29AAFE') ?>; color: #fff; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.85rem;">
                        Baca
                    </a>
                </div>

            </div>
        </div>
        <?php endforeach; ?>

    </div>
</div>
<?= $this->endSection() ?>