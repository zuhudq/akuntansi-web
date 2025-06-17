<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserStatusAndToken extends Migration
{
    public function up()
    {
        $fields = [
            'status' => [
                'type'       => "ENUM('pending_verification', 'pending_approval', 'active', 'inactive')",
                'default'    => 'pending_verification',
                'after'      => 'role'
            ],
            'verification_token' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'after'      => 'status'
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['status', 'verification_token']);
    }
}
