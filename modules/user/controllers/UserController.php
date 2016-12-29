<?php

namespace app\modules\user\controllers;

use app\controllers\RestController;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Controller for managing users.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class UserController extends RestController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'only' => ['view'],
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
                        'actions' => ['view'],
                        'roles' => ['@'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'options' => ['options'],
                    'view' => ['get'],
                ],
            ],
        ];
    }

    /**
     * @api {post} /users Create user
     * @apiVersion 1.0.0
     * @apiGroup User
     * @apiName  CreateUser
     * @apiDescription Creates new user and returns authentication token
     * @apiParam {String} email      User's email
     * @apiParam {String} password   User's password
     * @apiParam {String} inviteCode User's invite code
     * @apiPermission Guest
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 201 Created
     *     {
     *       "token": "f6asd54f98asd74f6vs6df54sdfg"
     *     }
     * @apiErrorExample {json} Validation Error:
     *     HTTP/1.1 422 Unprocessable Entity
     *     {
     *         "errors": {
     *             "email": ["First error"],
     *             "password": ["First error"]
     *         }
     *     }
     */
    public function actionCreate()
    {
        $model = new User();

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->register()) {
            \Yii::$app->response->setStatusCode(201);
            return ['token' => $model->authToken];
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    public function actionView($id)
    {
        $model = User::find()->byId($id)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}