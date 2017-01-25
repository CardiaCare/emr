<?php

use yii\db\Migration;

class m170125_101834_prefill_answer_types extends Migration
{
    public function safeUp()
    {
        $this->insert('answer_type', [
            'description' => 'Dichotomous'
        ]);

        $this->insert('answer_type', [
            'description' => 'Text'
        ]);

        $this->insert('answer_type', [
            'description' => 'SingleChoise'
        ]);

        $this->insert('answer_type', [
            'description' => 'BipolarQuestion'
        ]);

        $this->insert('answer_type', [
            'description' => 'MultipleChoise'
        ]);
    }

    public function safeDown()
    {
        $this->delete('answer_type', [
            'description' => 'Dichotomous'
        ]);

        $this->delete('answer_type', [
            'description' => 'Text'
        ]);

        $this->delete('answer_type', [
            'description' => 'SingleChoise'
        ]);

        $this->delete('answer_type', [
            'description' => 'BipolarQuestion'
        ]);

        $this->delete('answer_type', [
            'description' => 'MultipleChoise'
        ]);
    }
}
