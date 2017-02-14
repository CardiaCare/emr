<?php

namespace app\modules\user;

use yii\base\Module;

/**
 * User module.
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserModule extends Module
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->enableAutoLogin = false;
    }
}