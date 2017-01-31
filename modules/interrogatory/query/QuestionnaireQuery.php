<?php

namespace app\modules\interrogatory\query;

use yii\db\ActiveQuery;

class QuestionnaireQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return QuestionnaireQuery
     */
    public function byId(int $id) : QuestionnaireQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param int $doctorId
     * @return QuestionnaireQuery
     */
    public function byDoctorId(int $doctorId) : QuestionnaireQuery
    {
        return $this
            ->andWhere(['doctor_id' => $doctorId]);
    }
}
