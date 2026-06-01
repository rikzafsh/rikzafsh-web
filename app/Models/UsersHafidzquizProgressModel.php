<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersHafidzquizProgressModel extends Model
{
    protected $table            = 'users_hafidzquiz_progress';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // Daftar kolom yang diizinkan untuk diisi secara massal (mass assignment)
    protected $allowedFields    = [
        'user_id', 
        'hizb_number', 
        'thumn_number', 
        'node_number', 
        'subnode_number', 
        'highest_score', 
        'is_completed', 
        'updated_at'
    ];

    /**
     * Mengambil seluruh data progres milik satu user
     * Ini berguna untuk digambar ke Peta (Node) di sisi Frontend
     */
    public function getUnlockedNodes($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('hizb_number', 'ASC')
                    ->orderBy('thumn_number', 'ASC')
                    ->orderBy('node_number', 'ASC')
                    ->orderBy('subnode_number', 'ASC')
                    ->findAll();
    }
}