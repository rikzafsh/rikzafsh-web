<?php
namespace App\Models;
use CodeIgniter\Model;

class KomentarReaksiModel extends Model {
    protected $table = 'komentar_reaksi';
    protected $allowedFields = ['komentar_id', 'user_id', 'jenis'];
}