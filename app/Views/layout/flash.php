<?php
/**
 * Pesan flash terpusat (message / success / pesan / error / info / errors).
 * Disisipkan oleh layout; halaman tidak perlu menampilkannya sendiri.
 */
$flashes = [];

foreach (['message', 'success', 'pesan'] as $kunci) {
    if ($isi = session()->getFlashdata($kunci)) {
        $flashes[] = ['success', $isi];
    }
}
if ($isi = session()->getFlashdata('info')) {
    $flashes[] = ['info', $isi];
}
if ($isi = session()->getFlashdata('error')) {
    $flashes[] = ['error', $isi];
}

$daftarError = session()->getFlashdata('errors');
?>
<?php foreach ($flashes as [$tipe, $isi]): ?>
    <div class="alert alert--<?= esc($tipe, 'attr') ?>" role="<?= $tipe === 'error' ? 'alert' : 'status' ?>"<?= $tipe === 'success' ? ' data-autohide' : '' ?>>
        <span><?= esc(is_array($isi) ? implode(' ', $isi) : (string) $isi) ?></span>
        <button type="button" class="alert__close" aria-label="Tutup pesan">&times;</button>
    </div>
<?php endforeach ?>

<?php if (! empty($daftarError)): ?>
    <div class="alert alert--error" role="alert">
        <div>
            <strong>Mohon periksa kembali:</strong>
            <ul>
                <?php foreach ((array) $daftarError as $pesan): ?>
                    <li><?= esc(is_array($pesan) ? implode(' ', $pesan) : (string) $pesan) ?></li>
                <?php endforeach ?>
            </ul>
        </div>
        <button type="button" class="alert__close" aria-label="Tutup pesan">&times;</button>
    </div>
<?php endif ?>
