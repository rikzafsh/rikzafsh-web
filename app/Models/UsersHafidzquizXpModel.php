<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersHafidzquizXpModel extends Model
{
    protected $table            = 'users_hafidzquiz_xp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Daftar kolom yang diizinkan
    protected $allowedFields    = [
        'user_id', 
        'hizb_number', 
        'thumn_number', 
        'node_number', 
        'subnode_number', 
        'xp_gained', 
        'created_at'
    ];

    /**
     * FUNGSI SAKTI: Menghitung akumulasi total XP seorang user!
     * Tinggal panggil fungsi ini di Controller tanpa harus menyimpan total XP di tabel users.
     */
    public function getTotalXp($userId)
    {
        $result = $this->selectSum('xp_gained')
                       ->where('user_id', $userId)
                       ->first();
                       
        // Jika belum ada XP sama sekali, kembalikan nilai 0
        return $result['xp_gained'] ?? 0;
    }
}