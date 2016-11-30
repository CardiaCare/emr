<?php

namespace app\modules\survey\query;

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
}