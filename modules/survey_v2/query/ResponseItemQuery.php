<?php

namespace app\modules\survey_v2\query;

use yii\db\ActiveQuery;

class ResponseItemQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return ResponseItemQuery
     */
    public function byId(int $id) : ResponseItemQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $answerItemId
     * @return ResponseItemQuery
     */
    public function byAnswerItemId(int $answerItemId) : ResponseItemQuery
    {
        return $this
            ->andWhere(['answer_item_id' => $answerItemId]);
    }

    /**
     * @param int $responseId
     * @return ResponseItemQuery
     */
    public function byResponseId(int $responseId) : ResponseItemQuery
    {
        return $this->andWhere(['response_id' => $responseId]);
    }
}
