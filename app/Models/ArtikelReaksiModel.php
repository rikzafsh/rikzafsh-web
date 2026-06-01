<?php
namespace App\Models;
use CodeIgniter\Model;

class ArtikelReaksiModel extends Model {
    protected $table = 'artikel_reaksi';
    protected $allowedFields = ['artikel_id', 'user_id', 'jenis'];
}