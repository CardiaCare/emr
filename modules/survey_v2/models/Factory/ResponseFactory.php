<?php

namespace app\modules\survey_v2\models\Factory;

use app\modules\survey_v2\models\Response;

class ResponseFactory
{
    /**
     * @param array $data
     * @return Response[]
     */
    public function createListFromData(array $data)
    {
        $responses = [];

        foreach ($data as $item) {
            $responses[] = $this->createFromData($item);
        }

        return $responses;
    }

    /**
     * @param array $data
     * @return Response
     */
    public function createFromData(array $data)
    {
        $response = new Response(['_items' => $data['responseItems']]);
        $response->answer_id = $data['answer_id'];

        if (!empty($data['responseText'])) {
            $response->text = $data['responseText'];
        }

        if (!empty($data['responseFile'])) {
            $response->_fileUrl = $data['responseFile'];
        }

        return $response;
    }
}
