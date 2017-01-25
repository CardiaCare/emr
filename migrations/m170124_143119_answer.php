<?php

use yii\db\Migration;

class m170124_143119_answer extends Migration
{
    public function up()
    {
        $this->createTable('answer', [
            'id' => $this->primaryKey(),
            'answer_type_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
            'parent_answer_item_id' => $this->integer(),
            'uri' => $this->string(255),
        ]);

        $this->addForeignKey(
            'fk-answer-answer_type',
            'answer',
            'answer_type_id',
            'answer_type',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-answer-question',
            'answer',
            'question_id',
            'question',
            'id',
            'CASCADE'
        );

        $this->createTable('answer_item', [
            'id' => $this->primaryKey(),
            'answer_id' => $this->integer()->notNull(),
            'text' => $this->string(255)->notNull(),
            'score' => $this->integer()->notNull(),
            'uri' => $this->string(255),
        ]);

        $this->addForeignKey(
            'fk-answer_item-answer',
            'answer_item',
            'answer_id',
            'answer',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-answer-parent_answer_item',
            'answer',
            'parent_answer_item_id',
            'answer_item',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-answer_item-answer', 'answer_item');
        $this->dropForeignKey('fk-answer-parent_answer_item', 'answer');
        $this->dropForeignKey('fk-answer-answer_type', 'answer');
        $this->dropForeignKey('fk-answer-question', 'answer');
        $this->dropTable('answer_item');
        $this->dropTable('answer');
    }
}
