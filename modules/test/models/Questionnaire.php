<?php

namespace app\modules\test\models;

use app\modules\organization\models\Doctor;
use app\modules\test\models\Factory\QuestionFactory;
use app\modules\test\query\QuestionnaireQuery;
use yii\db\ActiveRecord;

class Questionnaire extends ActiveRecord
{
    public $_data;

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
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $questions = $this->getQuestionFactory()->createQuestionListFromData($this->_data['questions']);

        foreach ($questions as $question) {
            $this->link('questions', $question);
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

    /**
     * @return QuestionFactory
     */
    private function getQuestionFactory()
    {
        return new QuestionFactory();
    }
}
