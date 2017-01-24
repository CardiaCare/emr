<?php

use yii\db\Migration;

class m170124_140620_answer_type extends Migration
{
    public function up()
    {
        $this->createTable('answer_type', [
            'id' => $this->primaryKey(),
            'description' => $this->text()->notNull(),
            'uri' => $this->text()->unique(),
        ]);
    }

    public function down()
    {
        $this->dropTable('answer_type');
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
