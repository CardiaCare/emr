<?php

namespace app\modules\survey\controllers;

use app\controllers\RestController;
use app\modules\survey\models\Questionnaire;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class QuestionnaireController
 * @package app\modules\survey\controllers
 * @author Nikolai Lebedev
 */
class QuestionnaireController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'only' => ['update', 'index', 'view', 'delete'],
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['options'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'delete', 'index', 'view'],
                        'roles' => [User::ROLE_DOCTOR],
                    ]  
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'index' => ['get'],
                    'view' => ['get'],
                    'options' => ['options'],
                    'delete' => ['delete']
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        return Questionnaire::find()
            ->byDoctorId(\Yii::$app->user->identity->doctor->id)
            ->all();
    }

    public function actionView($id)
    {
        $model = Questionnaire::find()
            ->byId($id)
            ->byDoctorId(\Yii::$app->user->identity->doctor->id)
            ->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function actionCreate()
    {
        $model = new Questionnaire();

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
        $model = Questionnaire::find()
            ->byId($id)
            ->one();
        $model->delete();

        \Yii::$app->response->setStatusCode(204);
    }
    
        public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}