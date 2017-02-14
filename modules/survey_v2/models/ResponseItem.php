<?php

namespace app\modules\survey_v2\models;

use app\modules\survey_v2\models\Factory\ResponseFactory;
use app\modules\survey_v2\query\ResponseItemQuery;
use yii\db\ActiveRecord;

class ResponseItem extends ActiveRecord
{
    public $_subResponses;

    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['score', 'string'],
            ['answer_item_id', 'integer'],
            [['answer_item_id'], 'required', 'message' => '{attribute} не может быть пустым']
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!is_null($this->_subResponses) || !empty($this->_subResponses)) {
            $subResponses = $this->getResponseFactory()->createListFromData($this->_subResponses);

            foreach ($subResponses as $response) {
                $response->link('parentResponseItem', $this);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponse()
    {
        return $this->hasOne(Response::className(), ['id' => 'response_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSubResponses()
    {
        return $this->hasMany(Response::className(), ['parent_response_item_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAnswerItem()
    {
        return $this->hasOne(AnswerItem::className(), ['id' => 'answer_item_id']);
    }

    /**
     * @inheritdoc
     * @return ResponseItemQuery
     */
    public static function find()
    {
        return new ResponseItemQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'response_item';
    }

    /**
     * @return ResponseFactory
     */
    private function getResponseFactory()
    {
        return new ResponseFactory();
    }
}
