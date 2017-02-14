<?php

namespace app\modules\archive;

use yii\base\Module;

/**
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class ArchiveModule extends Module
{
    public function init()
    {
        parent::init();
        \Yii::$app->user->enableSession = false;
        \Yii::$app->user->enableAutoLogin = false;
    }
}