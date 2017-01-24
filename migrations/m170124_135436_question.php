<?php

use yii\db\Migration;

class m170124_135436_question extends Migration
{
    public function up()
    {
        $this->createTable('question', [
            'id' => $this->primaryKey(),
            'description' => $this->text()->notNull(),
            'questionnaire_id' => $this->integer()->notNull(),
            'uri' => $this->text()->notNull()->unique(),
            'number' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-question-que-id',
            'question',
            'questionnaire_id',
            'questionnaire',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-question-que-id', 'question');
        $this->dropTable('question');
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
