<?php

use yii\db\Migration;

class m170124_140620_answer_type extends Migration
{
    public function up()
    {
        $this->createTable('answer_type', [
            'id' => $this->primaryKey(),
            'description' => $this->text()->notNull(),
            'uri' => $this->string(255),
        ]);
    }

    public function down()
    {
        $this->dropTable('answer_type');
    }
}
