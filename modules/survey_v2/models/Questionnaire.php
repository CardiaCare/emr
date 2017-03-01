<?php

namespace app\modules\survey_v2\models;

use app\modules\emr\models\Patient;
use app\modules\organization\models\Doctor;
use app\modules\survey_v2\models\Factory\QuestionFactory;
use app\modules\survey_v2\query\QuestionnaireQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Questionnaire extends ActiveRecord
{
    public $_questions;

    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['emergency', 'boolean'],
            ['lang', 'string'],
            [['version', 'lang', 'emergency'], 'required', 'message' => '{attribute} не может быть пустым'],
            ['version', 'string'],
            ['description', 'string'],
            ['created_at', 'date'],
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
            $this->doctor_id = \Yii::$app->user->identity->id;

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $questions = $this->getQuestionFactory()->createQuestionListFromData($this->_questions);

        foreach ($questions as $key => $question) {
            $question->number = $key;
            $question->link('questionnaire', $this);
        }
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
        return $this->hasMany(Question::className(), ['questionnaire_id' => 'id']);
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

    /**
     * @return QuestionFactory
     */
    private function getQuestionFactory()
    {
        return new QuestionFactory();
    }
}
