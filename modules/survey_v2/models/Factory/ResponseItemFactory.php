<?php

namespace app\modules\survey_v2\models\Factory;

use app\modules\survey_v2\models\ResponseItem;

class ResponseItemFactory
{
    /**
     * @param array $data
     * @return ResponseItem[]
     */
    public function createListFromData(array $data)
    {
        $responseItems = [];

        foreach ($data as $item) {
            $responseItems[] = $this->createFromData($item);
        }

        return $responseItems;
    }

    /**
     * @param array $data
     * @return ResponseItem
     */
    public function createFromData(array $data)
    {
        $responseItem = new ResponseItem(['_subResponses' => $data['subResponses']]);
        $responseItem->answer_item_id = $data['linkedItems_id'];

        if (!empty($data['responseScore'])) {
            $responseItem->score = $data['responseScore'];
        }

        return $responseItem;
    }
}
