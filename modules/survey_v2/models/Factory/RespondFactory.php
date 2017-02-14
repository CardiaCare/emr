<?php

namespace app\modules\survey_v2\models\Factory;

use app\modules\survey_v2\models\Respond;

class RespondFactory
{
    /**
     * @param array $data
     * @return Respond[]
     */
    public function createListFromData(array $data)
    {
        $responds = [];

        foreach ($data as $item) {
            $responds[] = $this->createFromData($item);
        }

        return $responds;
    }

    /**
     * @param array $data
     * @return Respond
     */
    public function createFromData(array $data)
    {
        $response = new Respond(['_responses' => $data['responses']]);
        $response->question_id = $data['question_id'];

        return $response;
    }
}
