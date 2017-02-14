<?php

use yii\db\Migration;

class m170212_153545_files extends Migration
{
    public function up()
    {
        $this->createTable('local_file', [
            'id' => $this->primaryKey(),
            'path' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->createTable('response_file', [
            'id' => $this->primaryKey(),
            'url' => $this->string(255)->notNull(),
            'type' => $this->dateTime()->notNull(),
            'local_file_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-response_file-local_file',
            'response_file',
            'local_file_id',
            'local_file',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-response_file-local_file', 'response_file');
        $this->dropTable('response_file');
        $this->dropTable('local_file');
    }
}
