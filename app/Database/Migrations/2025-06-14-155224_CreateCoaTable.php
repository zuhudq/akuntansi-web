<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_akun' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_akun' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'nama_akun' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'posisi_saldo' => [ // Normal Balance Position
                'type' => 'ENUM("debit", "kredit")',
                'default' => 'debit',
            ],
            'kategori_akun' => [ // Account Category
                'type' => 'ENUM("Aset", "Liabilitas", "Ekuitas", "Pendapatan", "Beban")',
                'default' => 'Aset',
            ],
        ]);
        $this->forge->addKey('id_akun', true);
        $this->forge->createTable('chart_of_accounts');
    }

    public function down()
    {
        $this->forge->dropTable('chart_of_accounts');
    }
}
