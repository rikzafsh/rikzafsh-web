<?php
namespace App\Models;
use CodeIgniter\Model;

class KomentarModel extends Model {
    protected $table = 'komentar';
    protected $allowedFields = ['artikel_id', 'user_id', 'parent_id', 'isi_komentar'];
    protected $useTimestamps = true;

    public function getKomentarByArtikel($artikel_id, $current_user_id = "NULL") {
        
        // PERBAIKAN: Mengganti kutip ganda menjadi \' dan menambahkan false di akhir select()
        return $this->select('komentar.*, users.username, users.full_name, users.avatar,
            (SELECT COUNT(*) FROM komentar_reaksi WHERE komentar_id = komentar.id AND jenis = \'like\') as total_like,
            (SELECT COUNT(*) FROM komentar_reaksi WHERE komentar_id = komentar.id AND jenis = \'dislike\') as total_dislike,
            (SELECT jenis FROM komentar_reaksi WHERE komentar_id = komentar.id AND user_id = ' . $current_user_id . ') as reaksi_user', false)
                    ->join('users', 'users.id = komentar.user_id')
                    ->where('artikel_id', $artikel_id)
                    ->orderBy('komentar.created_at', 'ASC')
                    ->findAll();
    }
}