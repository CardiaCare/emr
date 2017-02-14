<?php

namespace app\modules\survey_v2\query;

use yii\db\ActiveQuery;

class ResponseQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return ResponseQuery
     */
    public function byId(int $id) : ResponseQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $answerId
     * @return ResponseQuery
     */
    public function byAnswerId(int $answerId) : ResponseQuery
    {
        return $this
            ->andWhere(['answer_id' => $answerId]);
    }

    /**
     * @param int $respondId
     * @return ResponseQuery
     */
    public function byRespondId(int $respondId) : ResponseQuery
    {
        return $this->andWhere(['respond_id' => $respondId]);
    }
}
