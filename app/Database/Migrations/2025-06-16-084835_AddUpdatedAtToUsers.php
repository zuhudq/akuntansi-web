<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
                'default' => null,
                'after' => 'created_at' // Letakkan setelah kolom created_at
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'updated_at');
    }
}
