<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyStatusColumnInUsers extends Migration
{
    public function up()
    {
        $this->forge->modifyColumn('users', [
            'status' => [
                'type'       => "ENUM('pending_verification', 'pending_approval', 'active', 'inactive', 'rejected')", // Tambahkan 'rejected'
                'default'    => 'pending_verification',
            ]
        ]);
    }

    public function down()
    {
        // Untuk rollback, kita kembalikan ke definisi lama
        $this->forge->modifyColumn('users', [
            'status' => [
                'type'       => "ENUM('pending_verification', 'pending_approval', 'active', 'inactive')",
                'default'    => 'pending_verification',
            ]
        ]);
    }
}
