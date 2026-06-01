<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersHafidzquizProgressTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'hizb_number' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'thumn_number' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'node_number' => [
                'type'       => 'INT',
                'constraint' => 3,
            ],
            'subnode_number' => [
                'type'       => 'INT',
                'constraint' => 2,
            ],
            'highest_score' => [
                'type'       => 'INT',
                'constraint' => 3,
                'default'    => 0,
            ],
            'is_completed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        // Mengaitkan FK user_id ke tabel users kolom id yang berjenis INT(11) UNSIGNED
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users_hafidzquiz_progress');
    }

    public function down()
    {
        $this->forge->dropTable('users_hafidzquiz_progress');
    }
}