<?php

use yii\db\Migration;

class m170129_235922_patient_questionnaire_join extends Migration
{
    public function up()
    {
        $this->createTable('patients_questionnaires', array(
            'patient_id' => $this->integer()->notNull(),
            'questionnaire_id' => $this->integer()->notNull(),
        ));
        $this->addForeignKey(
            'fk-que-patient-id',
            'patients_questionnaires',
            'patient_id',
            'patient',
            'id',
            'CASCADE'
        );
        $this->addForeignKey(
            'fk-que-que-id',
            'patients_questionnaires',
            'questionnaire_id',
            'questionnaire',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-que-patient-id', 'patients_questionnaires');
        $this->dropForeignKey('fk-que-que-id', 'patients_questionnaires');
        $this->dropTable('patients_questionnaires');
    }
}
