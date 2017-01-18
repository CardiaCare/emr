<?php

namespace app\modules\user\models;

use app\modules\emr\models\Patient;
use app\modules\organization\models\Doctor;
use app\modules\organization\models\Organization;
use app\modules\user\query\UserQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $email
 * @property string $password_hash
 * @property string $created_at
 * @property string $recovery_code
 * @property string $authToken
 *
 * @property Organization $organization
 * @property Patient $patient
 * @property Doctor $doctor
 * @property UserInvite[] $invites
 */
class User extends ActiveRecord implements IdentityInterface
{
    const ROLE_PATIENT = 'patient';
    const ROLE_DOCTOR = 'doctor';
    const ROLE_CHIEF = 'chief';

    /**
     * @var string
     */
    public $inviteCode;

    /**
     * @var string
     */
    public $password;

    /**
     * @var UserInvite
     */
    private $userInvite;

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function fields()
    {
        $fields = parent::fields();

        $fields['role'] = function (User $model) {
            return $model->getUserInvite()->role;
        };

        $fields['person'] = function (User $model) {
            switch ($model->getUserInvite()->role) {
                case self::ROLE_CHIEF:
                case self::ROLE_DOCTOR:
                    return $model->getDoctor();
                    break;
                case self::ROLE_PATIENT:
                    return $model->getPatient();
                    break;
            }
        };

        return $fields;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey) : bool
    {
        return false;
    }

    /**
     * Validates password and rehashes it if needed.
     *
     * @param  string $password
     * @return bool
     */
    public function validatePassword(string $password) : bool
    {
        if (password_verify($password, $this->password_hash)) {
            if (password_needs_rehash($this->password_hash, PASSWORD_DEFAULT)) {
                $this->updateAttributes([
                    'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                ]);
            }

            return true;
        }

        return false;
    }

    /**
     * Creates new auth token for this user.
     *
     * @return string
     */
    public function getAuthToken() : string
    {
        return (new UserToken(['user_id' => $this->id]))->create();
    }

    public function getUserInvite()
    {
        return UserInvite::find()->byReferralId($this->id)->one();
    }

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            [['email', 'inviteCode'], 'trim'],
            [['email', 'inviteCode', 'password'], 'required', 'message' => '{attribute} не может быть пустым'],
            ['email', 'email', 'message' => 'Некорректный формат Email адреса'],
            ['email', 'unique', 'message' => 'Email уже используется'],
            ['password', 'string', 'min' => 6, 'max' => 72,
                'tooShort' => 'Пароль не может быть короче 6 символов',
                'tooLong' => 'Пароль не может быть длиннее 72 символов'],
            ['inviteCode', 'validateInviteCode'],
        ];
    }

    /**
     * Checks invite code.
     * @param $attribute
     */
    public function validateInviteCode(string $attribute)
    {
        $this->userInvite = UserInvite::find()->byCode($this->inviteCode)->byEmail($this->email)->one();

        if ($this->userInvite === null) {
            $this->addError($attribute, 'Некорректный инвайт код');
        }
    }

    /**
     * Registers user and sends welcome message to email.
     *
     * @return bool
     */
    public function register() : bool
    {
        $transaction = $this->getDb()->beginTransaction();

        try {
            if (!$this->validate()) {
                return false;
            }

            $this->save(false);

            \Yii::$app->mailer->compose('register', ['user' => $this])
                ->setTo($this->email)
                ->setSubject('Регистрация в системе')
                ->setFrom(\Yii::$app->params['adminEmail'])
                ->send();

            $this->userInvite->updateAttributes(['referral_id' => $this->id]);

            $role = \Yii::$app->authManager->getRole($this->userInvite->role);
            \Yii::$app->authManager->assign($role, $this->id);

            switch ($this->userInvite->role) {
                case self::ROLE_CHIEF:
                    $organization = new Organization(['owner_id' => $this->id]);
                    $organization->save(false);
                    break;
                case self::ROLE_PATIENT:
                    (new Patient())->link('user', $this);
                    \Yii::$app->db->createCommand()->insert('patient_to_doctor', [
                        'patient_id' => $this->patient->id,
                        'doctor_id' => $this->userInvite->referrer->doctor->id,
                    ])->execute();
                    break;
                case self::ROLE_DOCTOR:
                    $doctor = new \app\modules\organization\models\Doctor([
                        'user_id' => $this->id,
                        'organization_id' => $this->userInvite->referrer->organization->id,
                    ]);
                    $doctor->save(false);
                    break;
            }

            $transaction->commit();

            return true;
        } catch (\Throwable $e) {
            \Yii::error($e);

            $transaction->rollBack();

            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if ($this->password != null) {
            $this->password_hash = password_hash($this->password, PASSWORD_DEFAULT);
        }

        if ($this->isNewRecord) {
            $this->created_at = new Expression('NOW()');
        }

        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganization()
    {
        return $this->hasOne(\app\modules\organization\models\Organization::className(), ['owner_id' => 'id'])->orderBy(['id' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPatient()
    {
        return $this->hasOne(Patient::className(), ['user_id' => 'id'])->orderBy(['id' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDoctor()
    {
        return $this->hasOne(Doctor::className(), ['user_id' => 'id'])->orderBy(['id' => SORT_ASC]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvites()
    {
        return $this->hasMany(UserInvite::className(), ['referral_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'password_hash' => 'Хэш пароля',
            'created_at' => 'Время создания',
            'recovery_code' => 'Код восстановления пароля',
            'password' => 'Пароль',
            'inviteCode' => 'Инвайт код',
        ];
    }

    /**
     * @inheritdoc
     * @return \app\modules\user\query\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return UserToken::find()->byCode($token)->one()->user ?? null;
    }
}
