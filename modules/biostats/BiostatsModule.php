<?php

namespace app\modules\biostats;

use yii\base\Module;

class BiostatsModule extends Module
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->enableAutoLogin = false;
    }
}
