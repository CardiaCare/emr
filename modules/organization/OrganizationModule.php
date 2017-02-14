<?php

namespace app\modules\organization;

use yii\base\Module;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class OrganizationModule extends Module
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->enableAutoLogin = false;
    }
}