<?php
namespace App\Models;
use CodeIgniter\Model;

class ArtikelModel extends Model
{
    protected $table            = 'artikel';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $allowedFields    = ['judul', 'slug', 'konten', 'thumbnail', 'kategori_id', 'penulis_id', 'status', 'views'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Apakah kolom `status` (draf/terbit) sudah ada di database?
     * Kolom ini dibuat oleh migrasi AddStatusToArtikel; selama belum dijalankan,
     * semua artikel dianggap terbit agar situs tetap berfungsi.
     */
    public static function adaKolomStatus(): bool
    {
        static $ada = null;

        if ($ada === null) {
            $ada = \Config\Database::connect()->fieldExists('status', 'artikel');
        }

        return $ada;
    }

    /** Batasi query ke artikel yang sudah terbit. */
    public function hanyaTerbit(): self
    {
        if (self::adaKolomStatus()) {
            $this->where('artikel.status', 'published');
        }

        return $this;
    }

    /** Select + join standar untuk kartu artikel (penulis, kategori, jumlah komentar). */
    public function kartu(): self
    {
        $this->select('artikel.*, users.username, users.full_name, users.avatar,
            kategori.nama_kategori, kategori.slug as kat_slug, kategori.colour as kat_colour, kategori.cover_image,
            (SELECT COUNT(*) FROM komentar WHERE komentar.artikel_id = artikel.id) as total_komentar', false)
            ->join('users', 'users.id = artikel.penulis_id', 'left')
            ->join('kategori', 'kategori.id = artikel.kategori_id', 'left');

        return $this;
    }
}
