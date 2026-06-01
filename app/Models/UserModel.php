<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

class UserModel extends ShieldUserModel
{
    protected function initialize(): void
    {
        // Jalankan inisialisasi dasar dari Shield terlebih dahulu
        parent::initialize();

        // Gabungkan kolom bawaan Shield dengan kolom kustom baru Anda
        $this->allowedFields = array_merge($this->allowedFields, [
            'full_name',
            'avatar',
            'bio',
        ]);
    }
}