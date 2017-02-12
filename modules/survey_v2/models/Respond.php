<?php

namespace app\modules\survey_v2\models;

use app\modules\survey_v2\models\Factory\ResponseFactory;
use app\modules\survey_v2\query\RespondQuery;
use yii\db\ActiveRecord;

class Respond extends ActiveRecord
{
    public $_responses;

    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['question_id', 'integer'],
            [['question_id'], 'required', 'message' => '{attribute} не может быть пустым']
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (!is_null($this->_responses)) {
            $responses = $this->getResponseFactory()->createListFromData($this->_responses);

            foreach ($responses as $response) {
                $response->link('respond', $this);
            }
        }
    }

    public function getFeedback()
    {
        return $this->hasOne(Feedback::className(), ['id' => 'feedback_id']);
    }

    public function getResponses()
    {
        return $this->hasMany(Response::className(), ['respond_id' => 'id']);
    }

    public function getQuestion()
    {
        return $this->hasOne(Question::className(), ['id' => 'question_id']);
    }

    /**
     * @return RespondQuery
     */
    public static function find()
    {
        return new RespondQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'respond';
    }

    /**
     * @return ResponseFactory
     */
    private function getResponseFactory()
    {
        return new ResponseFactory();
    }
}
