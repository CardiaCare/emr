<?php

use yii\db\Migration;

class m170214_112316_bloodpressure_fix_relation_to_patient extends Migration
{
    public function up()
    {
        $this->dropForeignKey('fk-blood-patient-id', 'bloodpressure');

        $this->addForeignKey(
            'fk-blood-patient',
            'bloodpressure',
            'patient_id',
            'patient',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-blood-patient', 'bloodpressure');

        $this->addForeignKey(
            'fk-blood-patient-id',
            'bloodpressure',
            'patient_id',
            'user',
            'id',
            'CASCADE'
        );
    }
}
