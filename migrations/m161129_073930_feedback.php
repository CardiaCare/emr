<?php

use yii\db\Migration;

class m161129_073930_feedback extends Migration
{
    public function up()
    {
        $this->createTable('feedback', array(
            'id' => $this->primaryKey(),
            'file' => $this->string()->notNull(),
            'patient_id' => $this->integer(),
            'created_at' => $this->integer()
        ));
        $this->addForeignKey(
            'fk-que-patient-id',
            'feedback',
            'patient_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-que-patient-id', 'feedback');
        $this->dropTable('feedback');
    }
}
