<?php

use yii\db\Migration;

class m170130_110048_add_emergency_delete_data_from_questionnaire extends Migration
{
    public function up()
    {
        $this->addColumn('questionnaire', 'emergency', $this->boolean()->notNull());
        //$this->dropColumn('questionnaire', 'data');
    }

    public function down()
    {
        $this->dropColumn('questionnaire', 'emergency');
        //$this->addColumn('questionnaire', 'data', $this->text()->notNull());
    }
}
