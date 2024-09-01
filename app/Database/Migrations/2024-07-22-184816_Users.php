<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
{
    public function up()
    {
        //
        $db = db_connect();
        $db->disableForeignKeyChecks();

        $this->forge->addField([

            'id' => [
                'type' => 'BIGINT',
                'unsigned' => true,
                'auto_increment' => true,
            ],

            'platformId' => [
                'type' => 'int',
                'unsigned' => true
            ],

            'name' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],

            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],

            'email' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => '20',
                'null' => true
            ],
            'password' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'token' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true
            ],
            'checked' => [
                'type' => 'BOOLEAN',
                'null' => false,
                'DEFAULT' => false
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ]
        ]);


        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('platformId', 'platform', 'id', 'NO ACTION', 'NO ACTION');
        $this->forge->createTable('users', true);
        $db->enableForeignKeyChecks();
    }

    public function down()
    {
        //
        $this->forge->dropTable('users', true);
    }
}
