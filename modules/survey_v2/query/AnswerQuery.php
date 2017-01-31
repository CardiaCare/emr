<?php

namespace app\modules\survey_v2\query;

use yii\db\ActiveQuery;

class AnswerQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return AnswerQuery
     */
    public function byId(int $id) : AnswerQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $questionId
     * @return AnswerQuery
     */
    public function byQuestionId(int $questionId) : AnswerQuery
    {
        return $this->andWhere(['question_id' => $questionId]);
    }

    /**
     * @param int $answerTypeId
     * @return AnswerQuery
     */
    public function byAnswerTypeId(int $answerTypeId) : AnswerQuery
    {
        return $this->andWhere(['answer_type_id' => $answerTypeId]);
    }
}
