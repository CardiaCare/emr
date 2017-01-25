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
            'uri' => $this->string(255),
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
}
