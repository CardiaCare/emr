<?php

namespace app\modules\test\models;

use app\modules\test\models\Factory\AnswerFactory;
use app\modules\test\query\AnswerItemQuery;
use yii\db\ActiveRecord;

class AnswerItem extends ActiveRecord
{
    public $_subAnswers;

    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['text', 'string'],
            ['score', 'int'],
            [['text', 'score'], 'required', 'message' => '{attribute} не может быть пустым'],
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

        $answers = $this->getAnswerFactory()->createAnswerListFromData($this->_subAnswers);

        foreach ($answers as $answer) {
            $answer->link('parentAnswerItem', $this);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubAnswers()
    {
        return $this->hasMany(Answer::className(), ['parent_answer_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswer()
    {
        return $this->hasOne(Answer::className(), ['id' => 'answer_id']);
    }

    /**
     * @inheritdoc
     * @return AnswerItemQuery
     */
    public static function find()
    {
        return new AnswerItemQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'answer_type';
    }

    /**
     * @return AnswerFactory
     */
    private function getAnswerFactory()
    {
        return new AnswerFactory();
    }
}
