<?php

use yii\db\Migration;

class m170129_200233_add_columns_to_questionnaire extends Migration
{
    public function up()
    {
        $this->addColumn('questionnaire', 'description', $this->string(255));
        $this->addColumn('questionnaire', 'created_at', $this->date()->notNull());
        $this->addColumn('questionnaire', 'lang', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('questionnaire', 'description');
        $this->dropColumn('questionnaire', 'created_at');
        $this->dropColumn('questionnaire', 'lang');
    }
}
