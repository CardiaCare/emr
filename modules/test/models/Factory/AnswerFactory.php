<?php

namespace app\modules\test\models\Factory;

use app\modules\test\models\Answer;
use app\modules\test\models\AnswerType;

class AnswerFactory
{
    private $requiredKeys = [
        'items',
        'type',
        'uri'
    ];

    /**
     * @param array $data
     * @return Answer[]
     */
    public function createAnswerListFromData(array $data)
    {
        $answers = [];

        foreach ($data as $item) {
            $answer = $this->createAnswerFromData($item);
            $answers[] = $answer;
        }

        return $answers;
    }

    /**
     * @param array $data
     * @return Answer
     */
    public function createAnswerFromData(array $data)
    {
        if (!$this->validateData($data)) {
            throw new \InvalidArgumentException('Some of these properties are not set: '.implode(', ',$this->requiredKeys).'.');
        }

        $answer = new Answer(['_items' => $data['items']]);
        $answer->uri = $data['uri'];
        $answer->answer_type_id = AnswerType::find()
            ->where(['description' => $data['type']])->one()->id;

        return $answer;
    }

    public function validateData(array $data)
    {
        if(count(array_intersect_key(array_flip($this->requiredKeys), $data)) === count($this->requiredKeys)) {
            return true;
        }

        return false;
    }
}
