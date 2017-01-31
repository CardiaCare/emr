<?php

namespace app\modules\interrogatory\models;

use app\modules\interrogatory\query\AnswerTypeQuery;
use yii\db\ActiveRecord;

class AnswerType extends ActiveRecord
{
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
     * @return \yii\db\ActiveQuery
     */
    public function getAnswers()
    {
        return $this->hasMany(Answer::className(), ['answer_type_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return AnswerTypeQuery
     */
    public static function find()
    {
        return new AnswerTypeQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'answer_type';
    }
}
