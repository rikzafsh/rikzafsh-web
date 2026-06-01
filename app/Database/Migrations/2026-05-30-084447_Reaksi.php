<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Reaksi extends Migration
{
    public function up()
    {
        // 1. TABEL REAKSI ARTIKEL
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'artikel_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis'      => ['type' => 'ENUM', 'constraint' => ['like', 'dislike']],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['artikel_id', 'user_id']); // 1 user hanya bisa beri 1 reaksi per artikel
        $this->forge->addForeignKey('artikel_id', 'artikel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('artikel_reaksi');

        // 2. TABEL REAKSI KOMENTAR / BALASAN
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'komentar_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'jenis'       => ['type' => 'ENUM', 'constraint' => ['like', 'dislike']],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['komentar_id', 'user_id']); // 1 user hanya bisa beri 1 reaksi per komentar
        $this->forge->addForeignKey('komentar_id', 'komentar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('komentar_reaksi');
    }

    public function down()
    {
        $this->forge->dropTable('komentar_reaksi');
        $this->forge->dropTable('artikel_reaksi');
    }
}  