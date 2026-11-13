<?php
/** @var string $message */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman tidak ditemukan | Ruang Rikza</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px; text-align: center;
            font-family: 'Segoe UI', system-ui, -apple-system, Arial, sans-serif; color: #2b2b2b;
            background: #703996;
        }
        .box { background: #fff; border-radius: 6px; padding: 40px 34px; max-width: 460px; width: 100%; }
        .code { font-family: Georgia, 'Times New Roman', serif; font-size: 4.5rem; font-weight: 700; line-height: 1; margin: 0 0 6px; color: #703996; }
        h1 { font-size: 1.4rem; margin: 0 0 10px; }
        p { color: #6b7280; margin: 0 0 24px; line-height: 1.6; }
        .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
        a { display: inline-block; padding: 10px 20px; border-radius: 4px; font-weight: 600; text-decoration: none; font-size: .95rem; }
        .primary { background: #3a43e0; color: #fff; }
        .ghost { border: 1px solid #d3d6e6; color: #703996; }
        small { display: block; margin-top: 22px; color: #9aa0b4; word-break: break-word; }
    </style>
</head>
<body>
    <div class="box">
        <p class="code">404</p>
        <h1>Halaman tidak ditemukan</h1>
        <p>Alamat yang kamu tuju tidak ada atau sudah dipindahkan. Coba kembali ke beranda atau jelajahi artikel terbaru.</p>
        <div class="actions">
            <a class="primary" href="<?= base_url('/') ?>">Ke beranda</a>
            <a class="ghost" href="<?= base_url('artikel') ?>">Lihat artikel</a>
        </div>
        <?php if (ENVIRONMENT !== 'production' && ! empty($message) && $message !== '(null)') : ?>
            <small><?= nl2br(esc($message)) ?></small>
        <?php endif ?>
    </div>
</body>
</html>
