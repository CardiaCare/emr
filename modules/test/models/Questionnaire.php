<?php

namespace app\modules\test\models;

use app\modules\organization\models\Doctor;
use app\modules\test\models\Factory\QuestionFactory;
use app\modules\test\query\QuestionnaireQuery;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class Questionnaire extends ActiveRecord
{
    public $_questions;

    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['data', 'string'],
            ['lang', 'string'],
            [['data', 'version', 'lang'], 'required', 'message' => '{attribute} не может быть пустым'],
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
                'value' => date('Y-m-d'),
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
