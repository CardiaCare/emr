<?php

namespace app\modules\survey\controllers;

use app\controllers\RestController;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;

/**
 * Class FeedbackController
 * @package app\modules\survey\controllers
 * @author Nikolai Lebedev
 */
class FeedbackController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => [User::ROLE_PATIENT],
                    ],
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {

    }

    public function actionView()
    {

    }

    public function actionCreate()
    {

    }

    public function actionUpdate()
    {

    }

    public function actionDelete()
    {

    }
}