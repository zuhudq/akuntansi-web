<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSaldoAwalToCoa extends Migration
{
    public function up()
    {
        $this->forge->addColumn('chart_of_accounts', [
            'saldo_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2', // Mendukung angka besar dan 2 angka di belakang koma
                'default'    => 0.00,
                'after'      => 'kategori_akun'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('chart_of_accounts', 'saldo_awal');
    }
}
