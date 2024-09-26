<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessagesSuporte extends Migration
{
    public function up()
    {
        //
        $db = db_connect();
        $db->disableForeignKeyChecks();

        $this->forge->addField([
            'id',
            'name',
            'type',
            'message',
            'created_at'
        ]);


        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('suporte', true);
        $db->enableForeignKeyChecks();
        
    }

    public function down()
    {
        //
    }
}
