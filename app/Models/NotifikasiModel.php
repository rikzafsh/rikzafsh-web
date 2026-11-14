<?php

namespace App\Models;

use CodeIgniter\Model;

class NotifikasiModel extends Model
{
    protected $table            = 'notifikasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['user_id', 'actor_id', 'tipe', 'artikel_id', 'komentar_id', 'dibaca', 'created_at'];
    protected $useTimestamps    = false;

    /**
     * Apakah tabel notifikasi sudah ada? Selama migrasi belum dijalankan,
     * semua fitur notifikasi dilewati dan situs tetap berjalan normal.
     */
    public static function tersedia(): bool
    {
        static $ada = null;

        if ($ada === null) {
            try {
                $ada = \Config\Database::connect()->tableExists('notifikasi');
            } catch (\Throwable $e) {
                $ada = false;
            }
        }

        return $ada;
    }

    /**
     * Mencatat notifikasi. Tidak membuat notifikasi untuk aksi pada diri sendiri.
     * Suka tidak dicatat dua kali untuk pelaku dan sasaran yang sama.
     * Kegagalan di sini tidak pernah menggagalkan aksi utama.
     */
    public static function catat(string $tipe, int $penerima, int $pelaku, ?int $artikelId, ?int $komentarId = null): void
    {
        if (! self::tersedia() || $penerima <= 0 || $penerima === $pelaku) {
            return;
        }

        try {
            $model = new self();

            if ($tipe === 'like_artikel' || $tipe === 'like_komentar') {
                $model->where('user_id', $penerima)->where('actor_id', $pelaku)->where('tipe', $tipe);

                if ($tipe === 'like_artikel') {
                    $model->where('artikel_id', $artikelId);
                } else {
                    $model->where('komentar_id', $komentarId);
                }

                if ($model->first() !== null) {
                    return;
                }

                $model = new self();
            }

            $model->insert([
                'user_id'     => $penerima,
                'actor_id'    => $pelaku,
                'tipe'        => $tipe,
                'artikel_id'  => $artikelId,
                'komentar_id' => $komentarId,
                'dibaca'      => 0,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mencatat notifikasi: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus notifikasi suka ketika suka dibatalkan.
     */
    public static function hapusSuka(string $tipe, int $penerima, int $pelaku, ?int $artikelId, ?int $komentarId = null): void
    {
        if (! self::tersedia()) {
            return;
        }

        try {
            $model = new self();
            $model->where('user_id', $penerima)->where('actor_id', $pelaku)->where('tipe', $tipe);

            if ($tipe === 'like_artikel') {
                $model->where('artikel_id', $artikelId);
            } else {
                $model->where('komentar_id', $komentarId);
            }

            $model->delete();
        } catch (\Throwable $e) {
            log_message('error', 'Gagal menghapus notifikasi: ' . $e->getMessage());
        }
    }

    public function jumlahBelumDibaca(int $userId): int
    {
        return $this->where('user_id', $userId)->where('dibaca', 0)->countAllResults();
    }

    /**
     * Select + join standar: nama pelaku, judul artikel, dan cuplikan komentar.
     */
    public function denganDetail(int $userId): self
    {
        $this->select('notifikasi.*, users.username as actor_username, users.full_name as actor_nama, users.avatar as actor_avatar,
            artikel.judul as artikel_judul, artikel.slug as artikel_slug, komentar.isi_komentar as komentar_isi')
            ->join('users', 'users.id = notifikasi.actor_id', 'left')
            ->join('artikel', 'artikel.id = notifikasi.artikel_id', 'left')
            ->join('komentar', 'komentar.id = notifikasi.komentar_id', 'left')
            ->where('notifikasi.user_id', $userId)
            ->orderBy('notifikasi.created_at', 'DESC')
            ->orderBy('notifikasi.id', 'DESC');

        return $this;
    }

    public function daftar(int $userId, int $batas = 6): array
    {
        return $this->denganDetail($userId)->findAll($batas);
    }

    /**
     * Alamat tujuan saat notifikasi dibuka (bagian halaman yang relevan).
     */
    public static function tujuan(array $n): string
    {
        if (empty($n['artikel_slug'])) {
            return site_url('notifikasi');
        }

        $url = site_url('artikel/' . $n['artikel_slug']);

        if ($n['tipe'] === 'like_artikel') {
            return $url . '#reaksi';
        }

        return $url . (! empty($n['komentar_id']) ? '#komentar-' . (int) $n['komentar_id'] : '#diskusi');
    }
}
