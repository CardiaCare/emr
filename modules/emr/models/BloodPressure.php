<?php

namespace app\modules\emr\models;

use app\modules\emr\models\Patient;
use app\modules\emr\query\BloodPressureQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;


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
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->patient_id = \Yii::$app->user->identity->patient->id;
            }

            return true;
        }

        return false;
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
