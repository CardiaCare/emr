<?php

namespace app\modules\survey\models;

use app\modules\emr\models\Patient;
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
            ['data', 'string'],
            [['data', 'version'], 'required', 'message' => '{attribute} не может быть пустым'],
            ['version', 'string'],
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->doctor_id = \Yii::$app->user->identity->doctor->id;
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['id' => 'doctor_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatients()
    {
        return $this->hasMany(Patient::className(), ['id' => 'patient_id'])
            ->viaTable('patients_questionnaires', ['questionnaire_id' => 'id']);
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