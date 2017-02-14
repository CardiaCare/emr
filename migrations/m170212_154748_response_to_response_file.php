<?php

use yii\db\Migration;

class m170212_154748_response_to_response_file extends Migration
{
    public function up()
    {
        $this->addColumn('response', 'response_file_id', $this->integer());
        $this->addColumn('response', 'has_file', $this->boolean());
        $this->addForeignKey(
            'fk-response-response_file',
            'response',
            'response_file_id',
            'response_file',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-response-response_file', 'response');
        $this->dropColumn('response', 'response_file_id');
        $this->dropColumn('response', 'has_file');
    }
}
