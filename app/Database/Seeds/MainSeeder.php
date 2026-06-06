<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;

class MainSeeder extends Seeder
{
    public function run()
    {
        $usersProvider = auth()->getProvider();
        $db = \Config\Database::connect();

        // ==========================================
        // 1. MEMBUAT BANYAK USER SEKALIGUS
        // ==========================================
        $dataUsers = [
            [
                'username'  => 'rikzafsh',
                'full_name' => 'Rikza F. Sh.',
                'email'     => 'rikzafsh@gmail.com',
                'password'  => 'rikzafriza22',
                'group'     => 'developer'
            ],
            [
                'username'  => 'uwaisandah',
                'full_name' => 'Uwais Andah Yurmansyah',
                'email'     => 'uwaisandah@rikzafsh.id',
                'password'  => 'uwais1435',
                'group'     => 'admin'
            ],
            [
                'username'  => 'armaputra',
                'full_name' => 'Arma Putra Pratama',
                'email'     => 'armaputra@rikzafsh.id',
                'password'  => 'arma1943',
                'group'     => 'user'
            ]
        ];

        foreach ($dataUsers as $data) {
            // Cek dulu biar tidak duplikat berdasarkan email
            if ($usersProvider->findByCredentials(['email' => $data['email']]) === null) {
                $user = new User([
                    'username'  => $data['username'],
                    'full_name' => $data['full_name'],
                    'email'     => $data['email'],
                    'password'  => $data['password'],
                ]);
                
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