<?php

namespace app\modules\emr\models;

use app\modules\emr\query\PatientQuery;
use app\modules\survey\models\Questionnaire;
use app\modules\user\models\User;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "patient".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property integer $user_id
 * @property integer $is_unknown
 * @property string $snils
 * @property string $inn
 * @property string $name
 * @property string $patronymic
 * @property string $surname
 * @property string $birthday
 * @property string $birthplace
 * @property integer $gender
 * @property Biosignal[] $biosignals
 * @property \app\modules\organization\models\Organization $organization
 * @property User $user
 * @property Test[] $tests
 */
class Patient extends ActiveRecord
{

    /**
     * @return array
     */
    public function rules() : array
    {
        return [
            ['is_unknown', 'boolean'],
            ['snils', 'string', 'max' => 12,
                'tooLong' => 'Снилс не может быть длиннее 12 символов'],
            ['inn', 'string', 'max' => 12,
                'tooLong' => 'ИНН не может быть длиннее 12 символов'],
            ['name', 'string', 'max' => 255,
                'tooLong' => 'Имя не может быть длиннее 255 символов'],
            ['patronymic', 'string', 'max' => 255,
                'tooLong' => 'Отчество не может быть длиннее 255 символов'],
            ['surname', 'string', 'max' => 255,
                'tooLong' => 'Фамилия не может быть длиннее 255 символов'],
            ['birthday', 'date', 'format' => 'php:Y-m-d'],
            ['gender', 'boolean'],

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
            'is_unknown' => 'Неизвестный',
            'snils' => 'СНИЛС',
            'inn' => 'ИНН',
            'name' => 'Имя',
            'patronymic' => 'Отчество',
            'surname' => 'Фамилия',
            'birthday' => 'День рождения',
            'birthplace' => 'Место рождения',
            'gender' => 'Пол',
        ];
    }

    public function getDoctors()
    {
        return $this->hasMany(Doctor::className(), ['id' => 'doctor_id'])
            ->viaTable('patient_to_doctor', ['patient_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBiosignals()
    {
        return $this->hasMany(Biosignal::className(), ['patient_id' => 'id']);
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
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionnaires()
    {
        return $this->hasMany(Questionnaire::className(), ['id' => 'questionnaire_id'])
            ->viaTable('patients_questionnaires', ['patient_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTests()
    {
        return $this->hasMany(Test::className(), ['patient_id' => 'id']);
    }

    /**
     * @inheritdoc
     * @return \app\modules\emr\query\PatientQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new PatientQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'patient';
    }
}
