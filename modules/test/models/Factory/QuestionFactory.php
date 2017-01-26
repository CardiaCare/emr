<?php

namespace app\modules\test\models\Factory;

use app\modules\test\models\Question;

class QuestionFactory
{
    private $requiredKeys = [
        'answer',
        'description',
        'uri'
    ];

    public function createQuestionListFromData(array $data)
    {
        $questions = [];

        foreach ($data as $item) {
            $question = $this->createQuestionFromData($item);
            $questions[] = $question;
        }

        return $questions;
    }

    public function createQuestionFromData(array $data)
    {
        if (!$this->validateData($data)) {
            throw new \InvalidArgumentException('Some of these properties are not set: '.implode($this->requiredKeys).'.');
        }

        $question = new Question(['_answer' => $data['answer']]);
        $question->load($data);

        return $question;
    }

    public function validateData(array $data)
    {
        if(count(array_intersect_key(array_flip($this->requiredKeys), $data)) !== count($this->requiredKeys)) {
            return true;
        }

        return false;
    }
}
