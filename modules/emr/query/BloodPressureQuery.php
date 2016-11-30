<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\emr\query;

use yii\db\ActiveQuery;


/**
 * Description of BloodPressureQuery
 *
 * @author Yulia Zavyalova
 */
class BloodPressureQuery extends ActiveQuery
{

    public function byId(int $id) : BloodPressureQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param  int $id
     * @return BloodPressureQuery
     */
    public function byPatientId(int $id) : BloodPressureQuery
    {
        return $this->andWhere(['bloodpressure.patient_id' => $id]);
    }


    /**
     * @inheritdoc
     * @return \app\modules\emr\models\BloodPressure[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\emr\models\BloodPressure|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
