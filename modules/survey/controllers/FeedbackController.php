<?php

namespace app\modules\survey\controllers;

use app\controllers\RestController;
use app\modules\survey\models\Feedback;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

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
                        'actions' => ['create', 'delete'],
                        'roles' => [User::ROLE_PATIENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'index'],
                        'roles' => [User::ROLE_PATIENT, User::ROLE_DOCTOR],
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
        return Feedback::find()->byPatientId(\Yii::$app->user->identity->patient->id)->all();
    }

    public function actionView($id)
    {
        $model = Feedback::find()
            ->byId($id)
            ->byPatientId(\Yii::$app->user->identity->patient->id)
            ->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function actionCreate()
    {
        $model = new Feedback();

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            \Yii::$app->response->setStatusCode(201);
            return $model;
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    public function actionDelete($id)
    {
        $model = Feedback::find()
            ->byId($id)
            ->one();
        $model->delete();

        \Yii::$app->response->setStatusCode(204);
    }
}