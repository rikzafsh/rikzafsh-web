<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\CLI\CLI;
use CodeIgniter\Shield\Entities\User;

class MainSeeder extends Seeder
{
    public function run()
    {
        $usersProvider = auth()->getProvider();
        $db = \Config\Database::connect();

        // ==========================================
        // 1. MEMBUAT BANYAK USER SEKALIGUS
        // Password diambil dari .env (SEED_PASSWORD_DEVELOPER, SEED_PASSWORD_ADMIN,
        // SEED_PASSWORD_USER). Jika kosong, dibuat acak dan ditampilkan sekali di terminal.
        // ==========================================
        $ambilPassword = static function (string $kunci): array {
            $dariEnv = env('SEED_PASSWORD_' . $kunci);

            return $dariEnv
                ? [$dariEnv, false]
                : [bin2hex(random_bytes(8)), true];
        };

        $dataUsers = [
            [
                'username'  => 'rikzafsh',
                'full_name' => 'Rikza F. Sh.',
                'email'     => 'rikzafsh@gmail.com',
                'password'  => 'DEVELOPER',
                'group'     => 'developer'
            ],
            [
                'username'  => 'uwaisandah',
                'full_name' => 'Uwais Andah Yurmansyah',
                'email'     => 'uwaisandah@rikzafsh.id',
                'password'  => 'ADMIN',
                'group'     => 'admin'
            ],
            [
                'username'  => 'armaputra',
                'full_name' => 'Arma Putra Pratama',
                'email'     => 'armaputra@rikzafsh.id',
                'password'  => 'USER',
                'group'     => 'user'
            ]
        ];

        foreach ($dataUsers as $data) {
            // Cek dulu biar tidak duplikat berdasarkan email
            if ($usersProvider->findByCredentials(['email' => $data['email']]) === null) {
                [$passwordAwal, $acak] = $ambilPassword($data['password']);

                $user = new User([
                    'username'  => $data['username'],
                    'full_name' => $data['full_name'],
                    'email'     => $data['email'],
                    'password'  => $passwordAwal,
                ]);

                if ($acak) {
                    CLI::write("Password awal untuk {$data['username']}: {$passwordAwal} (segera ganti)", 'yellow');
                }
                
                $usersProvider->save($user);

                // Ambil ID user yang baru masuk untuk disuntikkan grupnya
                $userId = $usersProvider->getInsertID();
                $userInstance = $usersProvider->findById($userId);
                
                $userInstance->addGroup($data['group']);
                $userInstance->activate();
            }
        }

        // ==========================================
        // 2. MEMBUAT BANYAK KATEGORI SEKALIGUS
        // ==========================================
        $builderKategori = $db->table('kategori');
        
        $dataKategori = [
            [
                'nama_kategori' => 'Kalendarologi',
                'slug'          => 'kalendarologi',
                'colour'        => '#29aafe',
                'cover_image'   => null,
                'deskripsi'     => 'Kategori tentang pembahasan sistem penanggalan.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Tipografi',
                'slug'          => 'tipografi',
                'colour'        => '#ff5722',
                'cover_image'   => null,
                'deskripsi'     => 'Kategori tentang desain huruf, font, dan aksara.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'nama_kategori' => 'Teknologi',
                'slug'          => 'teknologi',
                'colour'        => '#4caf50',
                'cover_image'   => null,
                'deskripsi'     => 'Kategori tentang perkembangan dunia software dan IT.',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]
        ];

        foreach ($dataKategori as $kat) {
            // Cek berdasarkan slug agar tidak duplikat saat seeder dijalankan ulang
            $cek = $builderKategori->getWhere(['slug' => $kat['slug']])->getRow();
            if (!$cek) {
                $builderKategori->insert($kat);
            }
        }
    }
}