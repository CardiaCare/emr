<?php

namespace app\modules\interrogatory\models;

use app\modules\interrogatory\models\Factory\AnswerFactory;
use app\modules\interrogatory\query\QuestionQuery;
use yii\db\ActiveRecord;

class Question extends ActiveRecord
{
    public $_answers;

    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['description', 'string'],
            [['description'], 'required', 'message' => '{attribute} не может быть пустым'],
            ['uri', 'string'],
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $answers = $this->getAnswerFactory()->createAnswerListFromData($this->_answers);

        foreach ($answers as $answer) {
            $answer->link('question', $this);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionnaire()
    {
        return $this->hasOne(Questionnaire::className(), ['id' => 'questionnaire_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['question_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return QuestionQuery
     */
    public static function find()
    {
        return new QuestionQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'question';
    }

    /**
     * @return AnswerFactory
     */
    private function getAnswerFactory()
    {
        return new AnswerFactory();
    }
}
