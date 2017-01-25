<?php

namespace app\modules\test\models;

use app\modules\organization\models\Doctor;
use app\modules\test\query\QuestionnaireQuery;
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
            $this->doctor_id = \Yii::$app->user->identity->id;
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
    public function getQuestions()
    {
        return $this->hasMany(Question::className(), ['questionnaire_id', 'id']);
    }

    /**
     * @inheritdoc
     * @return QuestionnaireQuery
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
