<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Menambah kolom `status` pada tabel artikel (draft / published).
 * Artikel yang sudah ada otomatis berstatus 'published'.
 */
class AddStatusToArtikel extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('status', 'artikel')) {
            return;
        }

        $this->forge->addColumn('artikel', [
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'published',
                'after'      => 'kategori_id',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('status', 'artikel')) {
            $this->forge->dropColumn('artikel', 'status');
        }
    }
}
