<?php

namespace app\modules\survey_v2\query;

use yii\db\ActiveQuery;

class RespondQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return RespondQuery
     */
    public function byId(int $id) : RespondQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $questionId
     * @return RespondQuery
     */
    public function byQuestionId(int $questionId) : RespondQuery
    {
        return $this
            ->andWhere(['question_id' => $questionId]);
    }

    /**
     * @param int $feedbackId
     * @return RespondQuery
     */
    public function byFeedbackId(int $feedbackId) : RespondQuery
    {
        return $this->andWhere(['feedback_id' => $feedbackId]);
    }
}
