<?php

namespace app\modules\survey_v2\models;

use app\modules\emr\models\Patient;
use app\modules\survey_v2\models\Factory\RespondFactory;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use app\modules\survey_v2\query\FeedbackQuery;
use yii\db\Expression;

class Feedback extends ActiveRecord
{
    public $_responds;

    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['lang', 'string'],
            ['questionnaire_id', 'integer'],
            [['questionnaire_id', 'lang'], 'required', 'message' => '{attribute} не может быть пустым'],
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
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if ($this->isNewRecord) {
                $this->patient_id = \Yii::$app->user->identity->patient->id;
            }

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

        if (!is_null($this->_responds)) {
            $responds = $this->getRespondFactory()->createListFromData($this->_responds);

            foreach ($responds as $respond) {
                $respond->link('feedback', $this);
            }
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getResponds()
    {
        return $this->hasMany(Respond::className(), ['feedback_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return FeedbackQuery
     */
    public static function find()
    {
        return new FeedbackQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableData()
    {
        return 'feedback';
    }

    /**
     * @return RespondFactory
     */
    private function getRespondFactory()
    {
        return new RespondFactory();
    }
}
