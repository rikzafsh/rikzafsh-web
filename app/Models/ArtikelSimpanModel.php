<?php
namespace App\Models;
use CodeIgniter\Model;

class ArtikelSimpanModel extends Model {
    protected $table = 'artikel_simpan';
    protected $allowedFields = ['artikel_id', 'user_id'];
    protected $useTimestamps = true;
    protected $updatedField  = ''; // Kita hanya butuh created_at
}