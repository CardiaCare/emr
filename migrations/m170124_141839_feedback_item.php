<?php

use yii\db\Migration;

class m170124_141839_feedback_item extends Migration
{
    public function up()
    {
        $this->createTable('feedback_item', [
            'feedback_id' => $this->integer()->notNull(),
            'question_id' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-feed-item-feedback',
            'feedback_item',
            'feedback_id',
            'feedback',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-feed-item-question',
            'feedback_item',
            'question_id',
            'question',
            'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk-feed-item-feedback', 'feedback_item');
        $this->dropForeignKey('fk-feed-item-question', 'feedback_item');
        $this->dropTable('feedback_item');
    }
}
