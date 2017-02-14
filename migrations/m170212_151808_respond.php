<?php

use yii\db\Migration;

class m170212_151808_respond extends Migration
{
    public function up()
    {
        $this->createTable('respond', [
            'id' => $this->primaryKey(),
            'feedback_id' => $this->integer()->notNull(),
            'question_id' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-respond-feedback',
            'respond',
            'feedback_id',
            'feedback',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-respond-question',
            'respond',
            'question_id',
            'question',
            'id',
            'CASCADE'
        );

        $this->createTable('response', [
            'id' => $this->primaryKey(),
            'respond_id' => $this->integer(),
            'answer_id' => $this->integer()->notNull(),
            'parent_response_item_id' => $this->integer(),
            'text' => $this->text(),
        ]);

        $this->addForeignKey(
            'fk-response-respond',
            'response',
            'respond_id',
            'respond',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-response-answer',
            'response',
            'answer_id',
            'answer',
            'id',
            'CASCADE'
        );

        $this->createTable('response_item', [
            'id' => $this->primaryKey(),
            'response_id' => $this->integer()->notNull(),
            'answer_item_id' => $this->integer()->notNull(),
            'score' => $this->integer(),
        ]);

        $this->addForeignKey(
            'fk-response_item-response',
            'response_item',
            'response_id',
            'response',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-response_item-answer_item',
            'response_item',
            'answer_item_id',
            'answer_item',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-response-response_item',
            'response',
            'parent_response_item_id',
            'response_item',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-response-response_item', 'response');
        $this->dropForeignKey('fk-response_item-response', 'response_item');
        $this->dropForeignKey('fk-response_item-answer_item', 'response_item');
        $this->dropTable('response_item');
        $this->dropForeignKey('fk-response-respond', 'response');
        $this->dropForeignKey('fk-response-answer', 'response');
        $this->dropTable('response');
        $this->dropForeignKey('fk-respond-feedback', 'respond');
        $this->dropForeignKey('fk-respond-question', 'respond');
        $this->dropTable('respond');
    }
}
