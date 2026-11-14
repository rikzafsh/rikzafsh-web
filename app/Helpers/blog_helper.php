<?php

/**
 * Helper tampilan blog: format tanggal Indonesia, waktu baca, ringkasan, dan avatar.
 * Dimuat otomatis lewat BaseController::$helpers.
 */

if (! function_exists('tgl_indo')) {
    /** Contoh: 13 Nov 2026 atau 13 Nov 2026, 09:30 */
    function tgl_indo(?string $tanggal, bool $denganJam = false): string
    {
        $ts = empty($tanggal) ? false : strtotime($tanggal);

        if ($ts === false) {
            return '-';
        }

        $bulan = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $hasil = date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);

        return $denganJam ? $hasil . ', ' . date('H:i', $ts) : $hasil;
    }
}

if (! function_exists('waktu_lalu')) {
    /** Contoh: "5 menit lalu"; lebih dari 30 hari ditampilkan sebagai tanggal. */
    function waktu_lalu(?string $tanggal): string
    {
        $ts = empty($tanggal) ? false : strtotime($tanggal);

        if ($ts === false) {
            return '-';
        }

        $selisih = time() - $ts;

        if ($selisih < 60) {
            return 'baru saja';
        }
        if ($selisih < 3600) {
            return floor($selisih / 60) . ' menit lalu';
        }
        if ($selisih < 86400) {
            return floor($selisih / 3600) . ' jam lalu';
        }
        if ($selisih < 2592000) {
            return floor($selisih / 86400) . ' hari lalu';
        }

        return tgl_indo($tanggal);
    }
}

if (! function_exists('teks_polos')) {
    /** Mengubah HTML artikel menjadi teks biasa (tanpa tag, spasi dirapikan). */
    function teks_polos(?string $html): string
    {
        $html  = str_ireplace(['</p>', '</li>', '</h1>', '</h2>', '</h3>', '</h4>', '<br>', '<br/>', '<br />'], ' ', (string) $html);
        $teks  = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $teks) ?? '');
    }
}

if (! function_exists('ringkasan')) {
    function ringkasan(?string $html, int $panjang = 140): string
    {
        return mb_strimwidth(teks_polos($html), 0, $panjang, '…', 'UTF-8');
    }
}

if (! function_exists('waktu_baca')) {
    /** Perkiraan menit membaca (200 kata per menit), minimal 1. */
    function waktu_baca(?string $html): int
    {
        $jumlah = preg_match_all('/\p{L}[\p{L}\p{N}\'’-]*/u', teks_polos($html));

        return max(1, (int) ceil(($jumlah ?: 0) / 200));
    }
}

if (! function_exists('avatar_html')) {
    /**
     * Mengembalikan tag <img> avatar, atau lingkaran berisi inisial jika tidak ada foto.
     * Hasilnya sudah di-escape dan aman dicetak langsung.
     *
     * @param string $ukuran xs|sm|md|lg|xl
     */
    function avatar_html(?string $file, ?string $nama, string $ukuran = 'md', string $kelas = ''): string
    {
        $ukuran = in_array($ukuran, ['xs', 'sm', 'md', 'lg', 'xl'], true) ? $ukuran : 'md';
        $nama   = trim((string) $nama) !== '' ? trim((string) $nama) : '?';
        $kelas  = trim('avatar avatar--' . $ukuran . ' ' . $kelas);

        if (! empty($file) && is_file(FCPATH . 'uploads/avatars/' . $file)) {
            return '<img class="' . esc($kelas, 'attr') . '" src="' . esc(base_url('uploads/avatars/' . $file), 'attr')
                . '" alt="' . esc($nama, 'attr') . '" loading="lazy">';
        }

        $inisial = mb_strtoupper(mb_substr($nama, 0, 1, 'UTF-8'), 'UTF-8');
        $palet   = ['#703996', '#29aafe', '#3a43e0', '#8e5cb5', '#1f8fd6', '#5b3fa8'];
        $warna   = $palet[abs(crc32($nama)) % count($palet)];

        return '<span class="' . esc($kelas . ' avatar--initial', 'attr') . '" style="background:' . $warna
            . '" aria-hidden="true">' . esc($inisial) . '</span>';
    }
}

if (! function_exists('nama_tampil')) {
    /** Nama lengkap jika ada, jika tidak pakai username. */
    function nama_tampil($fullName, $username): string
    {
        return trim((string) $fullName) !== '' ? (string) $fullName : (string) $username;
    }
}

if (! function_exists('edisi_romawi')) {
    function edisi_romawi($angka): string
    {
        $n   = (int) $angka;
        $res = '';
        foreach (['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1] as $simbol => $nilai) {
            $res .= str_repeat($simbol, intdiv($n, $nilai));
            $n   %= $nilai;
        }

        return $res !== '' ? $res : '-';
    }
}

if (! function_exists('periode_edisi')) {
    /** Contoh kode "2026T1" menjadi "Jan–Apr 2026". Selain pola itu: "Umum". */
    function periode_edisi(?string $kode): string
    {
        if (preg_match('/^(\d{4})T(\d)/i', (string) $kode, $m)) {
            $rentang = ['1' => 'Jan–Apr', '2' => 'May–Aug', '3' => 'Sep–Dec'];

            if (isset($rentang[$m[2]])) {
                return $rentang[$m[2]] . ' ' . $m[1];
            }
        }

        return 'Umum';
    }
}

if (! function_exists('warna_teks_kontras')) {
    /** Memilih teks putih atau gelap agar terbaca di atas warna latar hex. */
    function warna_teks_kontras(string $hex): string
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
        }
        if (strlen($hex) !== 6) {
            return '#ffffff';
        }

        $luminans = 0.299 * hexdec(substr($hex, 0, 2)) + 0.587 * hexdec(substr($hex, 2, 2)) + 0.114 * hexdec(substr($hex, 4, 2));

        return $luminans > 160 ? '#1d1d1d' : '#ffffff';
    }
}
