<?php

use yii\db\Migration;

class m161129_073904_questionnaire extends Migration
{
    public function up()
    {
        $this->createTable('questionnaire', array(
            'id' => $this->primaryKey(),
            'file' => $this->string()->notNull(),
            'doctor_id' => $this->integer()->notNull(),
            'data' => $this->text()->notNull(),
            'version' => $this->string()
        ));
        $this->addForeignKey(
            'fk-que-doctor-id',
            'questionnaire',
            'doctor_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-que-doctor-id', 'questionnaire');
        $this->dropTable('questionnaire');
    }
}
