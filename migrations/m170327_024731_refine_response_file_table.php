<?php

use yii\db\Migration;

class m170327_024731_refine_response_file_table extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk-response_file-local_file', 'response_file');
        $this->dropTable('local_file');
        $this->dropColumn('response_file', 'type');
        $this->dropColumn('response_file', 'local_file_id');
        $this->dropColumn('response_file', 'url');

        $this->addColumn('response_file', 'path', $this->string(255)->notNull()->unique());
        $this->addColumn('response_file', 'url', $this->string(255)->notNull()->unique());
        $this->addColumn('response_file', 'type', $this->string(255)->notNull());
        $this->addColumn('response_file', 'created_at', $this->dateTime()->notNull());
    }

    public function down()
    {
        $this->dropColumn('response_file', 'path');
        $this->dropColumn('response_file', 'created_at');

        $this->addColumn('response_file', 'local_file_id', $this->integer()->notNull());

        $this->createTable('local_file', [
            'id' => $this->primaryKey(),
            'path' => $this->string(255)->notNull(),
            'created_at' => $this->dateTime()->notNull(),
        ]);

        $this->delete('response_file');

        $this->addForeignKey(
            'fk-response_file-local_file',
            'response_file',
            'local_file_id',
            'local_file',
            'id',
            'CASCADE'
        );
    }
}
