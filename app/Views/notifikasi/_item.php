<?php
/**
 * Satu baris notifikasi (dipakai di dropdown header dan halaman Notifikasi).
 *
 * @var array $n Baris hasil NotifikasiModel::denganDetail()
 */
$nama  = nama_tampil($n['actor_nama'] ?? '', $n['actor_username'] ?? 'Seseorang');
$judul = $n['artikel_judul'] ?? 'sebuah artikel';

$frasa = match ($n['tipe']) {
    'like_artikel'  => 'menyukai artikelmu',
    'like_komentar' => 'menyukai komentarmu di',
    'komentar'      => 'mengomentari artikelmu',
    'balasan'       => 'membalas komentarmu di',
    default         => 'berinteraksi dengan',
};

$tampilkanCuplikan = in_array($n['tipe'], ['komentar', 'balasan', 'like_komentar'], true) && ! empty($n['komentar_isi']);
$belumDibaca       = ! (int) $n['dibaca'];
?>
<a class="notif-item<?= $belumDibaca ? ' is-unread' : '' ?>" href="<?= esc(base_url('notifikasi/buka/' . (int) $n['id']), 'attr') ?>">
    <?= avatar_html($n['actor_avatar'] ?? null, $nama, 'sm') ?>
    <span class="notif-item__body">
        <span class="notif-item__text"><strong><?= esc($nama) ?></strong> <?= esc($frasa) ?> <strong><?= esc($judul) ?></strong></span>
        <?php if ($tampilkanCuplikan): ?>
            <span class="notif-item__snippet">&ldquo;<?= esc(mb_strimwidth(teks_polos($n['komentar_isi']), 0, 110, '…', 'UTF-8')) ?>&rdquo;</span>
        <?php endif ?>
        <span class="notif-item__time"><?= esc(waktu_lalu($n['created_at'])) ?></span>
    </span>
    <?php if ($belumDibaca): ?><span class="notif-item__dot" aria-label="Belum dibaca"></span><?php endif ?>
</a>
