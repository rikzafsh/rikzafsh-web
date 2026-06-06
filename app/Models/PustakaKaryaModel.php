<?php

namespace App\Models;

use CodeIgniter\Model;

class PustakaKaryaModel extends Model
{
    protected $table            = 'pustaka_karya';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['codename', 'edisi', 'tema', 'tanggal_rilis', 'cover_img', 'warna', 'link'];
    protected $useTimestamps    = true;
}