<?php

use yii\db\Migration;

class m161129_073930_feedback extends Migration
{
    public function up()
    {
        $this->createTable('feedback', array(
            'id' => $this->primaryKey(),
            'patient_id' => $this->integer()->notNull(),
            'questionnaire_id' => $this->integer()->notNull(),
            'data' => $this->text()->notNull(),
            'created_at' => $this->dateTime()->notNull()
        ));
        $this->addForeignKey(
            'fk-que-patient-id',
            'feedback',
            'patient_id',
            'user',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-que-fee-id',
            'feedback',
            'questionnaire_id',
            'questionnaire',
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
