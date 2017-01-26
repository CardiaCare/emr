<?php

namespace app\modules\test\query;

use yii\db\ActiveQuery;

class AnswerItemQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return AnswerItemQuery
     */
    public function byId(int $id) : AnswerItemQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $answerId
     * @return AnswerItemQuery
     */
    public function byAnswerId(int $answerId) : AnswerItemQuery
    {
        return $this->andWhere(['answer_id' => $answerId]);
    }
}
