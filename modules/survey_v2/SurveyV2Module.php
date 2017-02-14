<?php

namespace app\modules\survey_v2;

use yii\base\Module;

class SurveyV2Module extends Module
{    
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->enableAutoLogin = false;
    }
}
