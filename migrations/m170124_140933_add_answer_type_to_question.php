<?php

use yii\db\Migration;

class m170124_140933_add_answer_type_to_question extends Migration
{
    public function up()
    {
        $this->addColumn('question', 'answer_type_id', $this->integer()->notNull());
        $this->addForeignKey(
            'fk-question-answer-type-id',
            'question',
            'answer_type_id',
            'answer_type',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-question-answer-type-id', 'question');
        $this->dropColumn('question', 'answer_type_id');
    }
}
