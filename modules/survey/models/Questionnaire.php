<?php

namespace app\modules\survey\models;

use app\modules\organization\models\Doctor;
use app\modules\survey\query\QuestionnaireQuery;
use yii\db\ActiveRecord;

class Questionnaire extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['file', 'string'],
            ['version', 'string'],
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\survey\query\QuestionnaireQuery
     */
    public static function find()
    {
        return new QuestionnaireQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'questionnaire';
    }
}