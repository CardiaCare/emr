<?php

namespace app\modules\survey_v2\models;

use app\modules\survey_v2\models\Factory\ResponseItemFactory;
use app\modules\survey_v2\query\ResponseQuery;
use yii\db\ActiveRecord;

class Response extends ActiveRecord
{
    public $_items;

    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['text', 'string'],
            ['answer_id', 'integer'],
            [['answer_id'], 'required', 'message' => '{attribute} не может быть пустым']
        );
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!is_null($this->_items) || !empty($this->_items)) {
            $responseItems = $this->getResponseItemFactory()->createListFromData($this->_items);

            foreach ($responseItems as $item) {
                $item->link('response', $this);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRespond()
    {
        return $this->hasOne(Respond::className(), ['id' => 'respond_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParentResponseItem()
    {
        return $this->hasOne(ResponseItem::className(), ['id' => 'parent_response_item_id']);
    }

    public function getResponseItems()
    {
        return $this->hasMany(ResponseItem::className(), ['response_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return ResponseQuery
     */
    public static function find()
    {
        return new ResponseQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'response';
    }

    /**
     * @return ResponseItemFactory
     */
    private function getResponseItemFactory()
    {
        return new ResponseItemFactory();
    }
}
