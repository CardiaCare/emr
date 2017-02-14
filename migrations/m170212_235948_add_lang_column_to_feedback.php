<?php

use yii\db\Migration;

class m170212_235948_add_lang_column_to_feedback extends Migration
{
    public function up()
    {
        $this->addColumn('feedback', 'lang', $this->string(255));
    }

    public function down()
    {
        $this->dropColumn('feedback', 'lang');
    }
}
