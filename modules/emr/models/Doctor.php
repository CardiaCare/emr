<?php

namespace app\modules\emr\models;

use app\modules\organization\models\Organization;
use app\modules\user\models\User;
use app\modules\emr\query\DoctorQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "doctor".
 *
 * @property integer      $id
 * @property integer      $user_id
 * @property integer      $organization_id
 * @property string       $name
 * @property string       $patronymic
 * @property string       $surname

 */
class Doctor extends ActiveRecord
{

    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['is_unknown', 'boolean'],
            ['name', 'string', 'max' => 255,
                'tooLong' => 'Имя не может быть длиннее 255 символов'],
            ['patronymic', 'string', 'max' => 255,
                'tooLong' => 'Отчество не может быть длиннее 255 символов'],
            ['surname', 'string', 'max' => 255,
                'tooLong' => 'Фамилия не может быть длиннее 255 символов'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'ID организации',
            'user_id' => 'ID пользователя',
            'name' => 'Имя',
            'patronymic' => 'Отчество',
            'surname' => 'Фамилия',
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(\app\modules\organization\models\Organization::className(), ['id' => 'organization_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }


    /**
     * @inheritdoc
     * @return \app\modules\emr\query\DoctorQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new DoctorQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'doctor';
    }
}
