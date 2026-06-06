<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePustakaKaryaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'codename' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'edisi' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tema' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'tanggal_rilis' => [
                'type' => 'DATE',
            ],
            'cover_img' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'warna' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'link' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        // Mengatur Primary Key
        $this->forge->addKey('id', true);

        // Membuat Tabel pustaka_karya
        $this->forge->createTable('pustaka_karya');
    }

    public function down()
    {
        // Menghapus tabel jika migration di-rollback
        $this->forge->dropTable('pustaka_karya');
    }
}