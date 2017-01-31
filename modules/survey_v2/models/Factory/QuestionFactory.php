<?php

namespace app\modules\survey_v2\models\Factory;

use app\modules\survey_v2\models\Question;

class QuestionFactory
{
    private $requiredKeys = [
        'answers',
        'description',
        'uri'
    ];

    /**
     * @param array $data
     * @return Question[]
     */
    public function createQuestionListFromData(array $data)
    {
        $questions = [];

        foreach ($data as $item) {
            $question = $this->createQuestionFromData($item);
            $questions[] = $question;
        }

        return $questions;
    }

    /**
     * @param array $data
     * @return Question
     */
    public function createQuestionFromData(array $data)
    {
        if (!$this->validateData($data)) {
            throw new \InvalidArgumentException('Question: Some of these properties are not set: '.implode(', ', $this->requiredKeys).'.');
        }

        $question = new Question(['_answers' => $data['answers']]);
        $question->description = $data['description'];
        $question->uri = $data['uri'];

        return $question;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validateData(array $data)
    {
        if(count(array_intersect_key(array_flip($this->requiredKeys), $data)) === count($this->requiredKeys)) {
            return true;
        }

        return false;
    }
}
