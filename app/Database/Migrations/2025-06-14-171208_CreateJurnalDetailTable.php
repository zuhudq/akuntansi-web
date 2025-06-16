<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJurnalDetailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_jurnal_detail' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'id_jurnal' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'id_akun' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true],
            'debit' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
            'kredit' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'default' => 0.00],
        ]);
        $this->forge->addKey('id_jurnal_detail', true);
        // Menambahkan foreign key
        $this->forge->addForeignKey('id_jurnal', 'jurnal_header', 'id_jurnal', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_akun', 'chart_of_accounts', 'id_akun', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('jurnal_detail');
    }

    public function down()
    {
        $this->forge->dropTable('jurnal_detail');
    }
}
