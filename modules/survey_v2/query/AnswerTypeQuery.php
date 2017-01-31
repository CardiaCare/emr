<?php

namespace app\modules\survey_v2\query;

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

    /**
     * @param string $description
     * @return AnswerTypeQuery
     */
    public function byDescription($description) : AnswerTypeQuery
    {
        return $this->andWhere(['description' => $description]);
    }

    /**
     * @param string $uri
     * @return AnswerTypeQuery
     */
    public function byUri($uri) : AnswerTypeQuery
    {
        return $this->andWhere(['uri' => $uri]);
    }
}
