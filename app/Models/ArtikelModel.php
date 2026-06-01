<?php
namespace App\Models;
use CodeIgniter\Model;

class ArtikelModel extends Model
{
    protected $table            = 'artikel';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    
    // 👇 TAMBAHKAN 'views' DI DALAM ARRAY INI 👇
    protected $allowedFields    = ['judul', 'slug', 'konten', 'thumbnail', 'kategori_id', 'penulis_id', 'status', 'views'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}