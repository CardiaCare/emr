<?php

namespace app\modules\emr\models;


use app\modules\emr\query\BloodPressureQuery;
use yii\db\ActiveRecord;


/**
 * Description of BloodPressure
 *
 * @author Yulia Zavyalova
 */
class BloodPressure extends ActiveRecord
{
    //put your code here
    
        /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['systolic', 'integer'],
            ['diastolic', 'integer']
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\emr\query\BloodPressureQuery
     */
    public static function find()
    {
        return new BloodPressureQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bloodpressure';
    }

}
