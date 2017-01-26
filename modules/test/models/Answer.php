<?php

namespace app\modules\test\models;

use app\modules\test\models\Factory\AnswerItemFactory;
use app\modules\test\query\AnswerQuery;
use yii\db\ActiveRecord;

class Answer extends ActiveRecord
{
    public $_items;

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
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $answerItems = $this->getAnswerItemFactory()->createAnswerItemListFromData($this->_items);

        foreach ($answerItems as $answerItem) {
            $this->link('items', $answerItem);
        }
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
     * @return \yii\db\ActiveQuery
     */
    public function getParentAnswerItem()
    {
        return $this->hasOne(AnswerItem::className(), ['id' => 'parent_answer_item_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getItems()
    {
        return $this->hasMany(AnswerItem::className(), ['answer_id' => 'id']);
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

    /**
     * @return AnswerItemFactory
     */
    private function getAnswerItemFactory()
    {
        return new AnswerItemFactory();
    }
}
