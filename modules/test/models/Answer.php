<?php

namespace app\modules\test\models;

use app\modules\test\query\AnswerQuery;
use yii\db\ActiveRecord;

class Answer extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerType()
    {
        return $this->hasOne(AnswerType::className(), ['id' => 'answer_type_id']);
    }

    /**
     * @inheritdoc
     * @return AnswerQuery
     */
    public static function find()
    {
        return new AnswerQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'answer';
    }
}
