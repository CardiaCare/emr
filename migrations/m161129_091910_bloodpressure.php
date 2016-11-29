<?php

use yii\db\Migration;

class m161129_091910_bloodpressure extends Migration
{
    public function up()
    {
        $this->createTable('bloodpressure', array(
            'id' => $this->primaryKey(),
            'patient_id' => $this->integer(),
            'systolic' => $this->integer()->notNull(),
            'diastolic' => $this->integer()->notNull(),
            'created_at' => $this->integer()
        ));
        $this->addForeignKey(
            'fk-que-patient-id',
            'bloodpressure',
            'patient_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-que-patient-id', 'bloodpressure');
        $this->dropTable('bloodpressure');
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
