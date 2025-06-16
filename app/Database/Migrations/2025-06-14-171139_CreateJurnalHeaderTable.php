<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJurnalHeaderTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jurnal' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'tanggal_jurnal' => ['type' => 'DATE'],
            'deskripsi' => ['type' => 'TEXT'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true, 'default' => null],
        ]);
        $this->forge->addKey('id_jurnal', true);
        $this->forge->createTable('jurnal_header');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_header');
    }
}
