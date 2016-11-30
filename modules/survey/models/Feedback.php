<?php
/**
 * Created by PhpStorm.
 * User: nikolay
 * Date: 11/28/16
 * Time: 1:36 PM
 */

namespace app\modules\survey\models;


use app\modules\emr\models\Patient;
use app\modules\survey\query\FeedbackQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

class Feedback extends ActiveRecord
{
    /**
     * @return array
     */
    public function rules() : array
    {
        return array(
            ['data', 'string'],
            ['questionnaire_id', 'integer'],
            [['data', 'questionnaire_id'], 'required', 'message' => '{attribute} не может быть пустым']
        );
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->isNewRecord) {
            $this->patient_id = \Yii::$app->user->identity->id;
            $this->created_at = new Expression('NOW()');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['id' => 'patient_id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\survey\query\FeedbackQuery
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
}