<?php

namespace app\modules\interrogatory\query;

use yii\db\ActiveQuery;

class QuestionQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return QuestionQuery
     */
    public function byId(int $id) : QuestionQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $questionnaireId
     * @return QuestionQuery
     */
    public function byQuestionnaireId(int $questionnaireId) : QuestionQuery
    {
        return $this
            ->andWhere(['questionnaire_id' => $questionnaireId]);
    }
}
