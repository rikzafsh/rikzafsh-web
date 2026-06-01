<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Artikel extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'judul'       => ['type' => 'VARCHAR', 'constraint' => 255],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 255, 'unique' => true],
            'konten'      => ['type' => 'TEXT'], // TEXT bisa menyimpan format HTML dari editor
            'thumbnail'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'kategori'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'penulis_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true], // Mengaitkan artikel dengan user
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        
        $this->forge->addKey('id', true);
        // Membuat relasi agar penulis_id terhubung dengan ID user dari Shield
        $this->forge->addForeignKey('penulis_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('artikel');
    }

    public function down()
    {
        $this->forge->dropTable('artikel');
    }
}
