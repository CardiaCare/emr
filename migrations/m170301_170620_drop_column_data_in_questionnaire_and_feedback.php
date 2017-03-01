<?php

use yii\db\Migration;

class m170301_170620_drop_column_data_in_questionnaire_and_feedback extends Migration
{
    public function up()
    {
        $this->dropColumn('questionnaire', 'data');
        $this->dropColumn('feedback', 'data');
    }

    public function down()
    {
        $this->addColumn('questionnaire', 'data', $this->text()->notNull());
        $this->addColumn('feedback', 'data', $this->text()->notNull());
    }
}
