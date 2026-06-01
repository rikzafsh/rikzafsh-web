<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class TambahViewsDanSimpan extends Migration
{
    public function up()
    {
        // 1. Tambah kolom views ke tabel artikel yang sudah ada
        $fields = [
            'views' => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'konten']
        ];
        $this->forge->addColumn('artikel', $fields);

        // 2. Buat tabel baru untuk fitur Simpan Artikel (Bookmark)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'artikel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['artikel_id', 'user_id']); // 1 user hanya bisa simpan 1 artikel sekali
        $this->forge->addForeignKey('artikel_id', 'artikel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('artikel_simpan');
    }

    public function down()
    {
        $this->forge->dropTable('artikel_simpan');
        $this->forge->dropColumn('artikel', 'views');
    }
}