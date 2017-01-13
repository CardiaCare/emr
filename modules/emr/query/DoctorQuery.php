<?php

namespace app\modules\emr\query;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\modules\emr\models\Doctor]].
 *
 * @see \app\models\Doctor
 */
class DoctorQuery extends ActiveQuery
{
    /**
     * @param  int $id
     * @return DoctorQuery
     */
    public function byId(int $id) : DoctorQuery
    {
        return $this->andWhere(['id' => $id]);
    }

    /**
     * @param  int $id
     * @return DoctorQuery
     */
    public function byOrganizationId(int $id) : DoctorQuery
    {
        return $this->andWhere(['organization_id' => $id]);
    }


    /**
     * @param  int $id
     * @return DoctorQuery
     */
    public function byUserId(int $id) : DoctorQuery
    {
        return $this->andWhere(['user_id' => $id]);
    }

    /**
     * @inheritdoc
     * @return \app\modules\emr\models\Doctor[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\modules\emr\models\Doctor|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
