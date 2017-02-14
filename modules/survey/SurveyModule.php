<?php

namespace app\modules\survey;

use yii\base\Module;

/**
 * Class SurveyModule
 * @package app\modules\survey
 * @author Nikolai Lebedev
 */
class SurveyModule extends Module
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->enableAutoLogin = false;
    }
}