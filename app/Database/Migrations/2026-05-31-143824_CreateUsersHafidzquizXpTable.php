<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersHafidzquizXpTable extends Migration
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
            'xp_gained' => [
                'type'       => 'INT',
                'constraint' => 5, 
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('users_hafidzquiz_xp');
    }

    public function down()
    {
        $this->forge->dropTable('users_hafidzquiz_xp');
    }
}