<?php

namespace app\modules\survey_v2\query;

use yii\db\ActiveQuery;

class FeedbackQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return FeedbackQuery
     */
    public function byId(int $id) : FeedbackQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $patientId
     * @return FeedbackQuery
     */
    public function byPatientId(int $patientId) : FeedbackQuery
    {
        return $this
            ->andWhere(['patient_id' => $patientId]);
    }

    /**
     * @param int $questionnaireId
     * @return FeedbackQuery
     */
    public function byQuestionnaireId(int $questionnaireId) : FeedbackQuery
    {
        return $this->andWhere(['questionnaire_id' => $questionnaireId]);
    }
}
