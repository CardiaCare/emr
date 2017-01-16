<?php

namespace app\modules\user\forms;

use app\modules\user\models\User;
use app\modules\user\models\UserToken;
use yii\base\Model;

/**
 * Login form.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class LoginForm extends Model
{
    /**
     * @var User
     */
    public $user;
    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $authToken;

    /**
     * @return bool
     */
    public function authenticate() : bool
    {
        if (!$this->validate()) {
            return false;
        }
        
        $user = User::find()->byEmail($this->email)->one();

        $this->user = $user;
        $this->authToken = (new UserToken(['user_id' => $user->id]))->create();
        
        return true;
    }

    /**
     * @inheritdoc
     */
    public function rules() : array
    {
        return [
            ['email', 'trim'],
            [['email', 'password'], 'required', 'message' => '{attribute} не может быть пустым'],
            ['email', 'email', 'message' => 'Некорректный формат Email адреса'],
            ['password', 'string', 'min' => 6, 'max' => 72,
                'tooShort' => 'Пароль не может быть короче 6 символов',
                'tooLong' => 'Пароль не может быть длиннее 72 символов'],
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates if password is correct.
     * 
     * @param $attribute
     */
    public function validatePassword($attribute)
    {
        $user = User::find()->byEmail($this->email)->one();

        if ($user === null || !$user->validatePassword($this->password)) {
            $this->addError($attribute, 'Некорректный пароль');
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() : array
    {
        return [
            'email'    => 'Email',
            'password' => 'Пароль',
        ];
    }
}