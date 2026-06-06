<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfileFieldsToUsers extends Migration
{
    public function up()
    {
        $fields = [
            // 1. Buat full_name dulu setelah username
            'full_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'username',
            ],
            // 2. Sekarang avatar aman diletakkan setelah full_name
            'avatar' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'full_name' 
            ],
            // 3. Terakhir bio diletakkan setelah avatar
            'bio' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'avatar'
            ],
        ];
        
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['avatar', 'bio', 'full_name']);
    }
}