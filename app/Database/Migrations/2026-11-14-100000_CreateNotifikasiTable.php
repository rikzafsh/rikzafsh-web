<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Tabel notifikasi: suka artikel, suka komentar, komentar baru, dan balasan.
 * Notifikasi ikut terhapus otomatis bila artikel, komentar, atau pengguna terkait dihapus.
 */
class CreateNotifikasiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],                 // penerima
            'actor_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],                 // pelaku
            'tipe'        => ['type' => 'VARCHAR', 'constraint' => 30],                                 // like_artikel | like_komentar | komentar | balasan
            'artikel_id'  => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'komentar_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'dibaca'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['user_id', 'dibaca']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('actor_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('artikel_id', 'artikel', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('komentar_id', 'komentar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('notifikasi', true);
    }

    public function down()
    {
        $this->forge->dropTable('notifikasi', true);
    }
}
