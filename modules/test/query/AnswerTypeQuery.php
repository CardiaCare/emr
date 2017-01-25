<?php

namespace app\modules\test\query;

use yii\db\ActiveQuery;

class AnswerTypeQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return AnswerTypeQuery
     */
    public function byId(int $id) : AnswerTypeQuery
    {
        return $this->andWhere(['id' => $id]);
    }
}
