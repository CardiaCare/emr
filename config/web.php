<?php

use app\modules\archive\ArchiveModule;
use app\modules\emr\EmrModule;
use app\modules\organization\OrganizationModule;
use app\modules\survey\SurveyModule;
use app\modules\user\UserModule;

$config = [
    'id' => 'api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'modules' => [
        'user' => UserModule::class,
        'emr' => EmrModule::class,
        'organization' => OrganizationModule::class,
        'archive' => ArchiveModule::class,
        'survey' => SurveyModule::class
    ],
    'components' => [
        'request' => [
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'response' => [
            'format' => 'json'
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                // user module
                'POST users' => 'user/user/create',
                'OPTIONS users' => 'user/user/options',
                'GET users/<id:\d+>' => 'user/user/view',
                'OPTIONS users/<id:\d+>' => 'user/user/options',

                'POST tokens' => 'user/token/create',
                'DELETE tokens' => 'user/token/delete',
                'OPTIONS tokens' => 'user/token/options',


                'POST invites' => 'user/invite/create',
                'OPTIONS invites' => 'user/invite/options',
                'GET invites' => 'user/invite/index',
                'GET invites/<id:\d+>' => 'user/invite/view',
                'DELETE invites/<id:\d+>' => 'user/invite/delete',
                'OPTIONS invites/<id:\d+>' => 'user/invite/options',

                'POST recovery' => 'user/recovery/request',
                'OPTIONS recovery' => 'user/recovery/options',
                'PUT user/password' => 'user/recovery/recover',

                // emr module
                'POST tests' => 'emr/test/create',
                'OPTIONS tests' => 'emr/test/options',
                'GET tests' => 'emr/test/index',
                'GET tests/<id:\d+>' => 'emr/test/index',
                'OPTIONS tests/<id:\d+>' => 'emr/test/options',

                'POST biosignals' => 'emr/biosignal/create',
                'OPTIONS biosignals' => 'emr/biosignal/options',

                'GET patients' => 'emr/patient/index',
                'OPTIONS patients' => 'emr/patient/options',
                'GET patients/<id:\d+>' => 'emr/patient/view',
                'PUT patients/<id:\d+>' => 'emr/patient/update',
                'DELETE patients/<id:\d+>' => 'emr/patient/delete',
                'OPTIONS patients/<id:\d+>' => 'emr/patient/options',
                
                
                'GET doctors' => 'emr/doctor/index',
                'OPTIONS doctors' => 'emr/doctor/options',
                'GET doctors/<id:\d+>' => 'emr/doctor/view',
                'PUT doctors/<id:\d+>' => 'emr/doctor/update',
                'DELETE doctors/<id:\d+>' => 'emr/doctor/delete',
                'OPTIONS doctors/<id:\d+>' => 'emr/doctor/options',

                // organization module
                'GET organizations' => 'organization/organization/view',
                'PUT organizations' => 'organization/organization/update',
                'OPTIONS organizations' => 'emr/organizations/options',


                // archive module
                'GET archive/organizations' => 'archive/organization/index',
                'OPTIONS  archive/organizations' => 'archive/organizations/options',
                'GET archive/organizations/<id:\d+>/revision/<revision:\d+>' => 'archive/organization/view',
                'OPTIONS  archive/organizations/<id:\d+>/revision/<revision:\d+>' => 'archive/organizations/options',

                'GET archive/patients' => 'archive/patient/index',
                'OPTIONS  archive/patients' => 'archive/patients/options',
                'GET archive/patients/<id:\d+>/revision/<revision:\d+>' => 'archive/patient/view',
                'OPTIONS  archive/patients/<id:\d+>/revision/<revision:\d+>' => 'archive/patients/options',

                // survey methods

                'POST survey' => 'survey/questionnaire/create',
                'OPTIONS survey' => 'survey/questionnaire/options',
                'GET survey' => 'survey/questionnaire/index',
                'GET survey/<id:\d+>' => 'survey/questionnaire/view',
                'OPTIONS survey/<id:\d+>' => 'survey/questionnaire/options',
                'DELETE survey/<id:\d+>' => 'survey/questionnaire/delete',

                'POST patients/<patientid:\d+>/feedback' => 'survey/feedback/create',
                'OPTIONS patients/<patientid:\d+>/feedback' => 'survey/feedback/options',
                'GET patients/<patientid:\d+>/feedback' => 'survey/feedback/index',
                'GET patients/<patientid:\d+>/feedback/<id:\d+>' => 'survey/feedback/view',
                'OPTIONS patients/<patientid:\d+>/feedback/<id:\d+>' => 'survey/feedback/options',
                'DELETE patients/<patientid:\d+>/feedback/<id:\d+>' => 'survey/feedback/delete',

                // blood pressure methods
                'POST patients/<patientid:\d+>/bloodpressure' => 'emr/bloodpressure/create',
                'OPTIONS patients/<patientid:\d+>/bloodpressure' => 'emr/bloodpressure/options',
                'GET patients/<patientid:\d+>/bloodpressure' => 'emr/bloodpressure/index',
                'GET patients/<patientid:\d+>/bloodpressure/<id:\d+>' => 'emr/bloodpressure/view',
                'OPTIONS patients/<patientid:\d+>/bloodpressure/<id:\d+>' => 'emr/bloodpressure/options',
                'DELETE patients/<patientid:\d+>/bloodpressure/bloodpressure/<id:\d+>' => 'emr/bloodpressure/delete'
            ],
        ],
    ],
];

return $config;
