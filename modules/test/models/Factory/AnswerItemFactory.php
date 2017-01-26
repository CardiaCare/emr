<?php

namespace app\modules\test\models\Factory;

use app\modules\test\models\AnswerItem;

class AnswerItemFactory
{
    private $requiredKeys = [
        'itemScore',
        'itemText',
        'subAnswers',
        'uri'
    ];

    public function createAnswerItemListFromData(array $data)
    {
        $answerItems = [];

        foreach ($data as $item) {
            $answerItem = $this->createAnswerItemFromData($item);
            $answerItems[] = $answerItem;
        }

        return $answerItems;
    }

    public function createAnswerItemFromData(array $data)
    {
        if (!$this->validateData($data)) {
            throw new \InvalidArgumentException('Some of these properties are not set: '.implode($this->requiredKeys).'.');
        }

        $answerItem = new AnswerItem(['_subAnswers' => $data['subAnswers']]);
        $answerItem->score = $data['score'];
        $answerItem->text = $data['text'];
        $answerItem->uri = $data['uri'];

        return $answerItem;
    }

    public function validateData(array $data)
    {
        if(count(array_intersect_key(array_flip($this->requiredKeys), $data)) !== count($this->requiredKeys)) {
            return true;
        }

        return false;
    }
}
