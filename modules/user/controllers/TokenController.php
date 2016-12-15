<?php

namespace app\modules\user\controllers;

use app\controllers\RestController;
use app\modules\user\forms\LoginForm;
use app\modules\user\models\UserToken;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * Controller for managing auth tokens.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class TokenController extends RestController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'only' => ['delete'],
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create', 'options'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['delete'],
                    'options' => ['options']
                ],
            ],
        ];
    }

    /**
     * @api {post} /tokens Create token
     * @apiVersion 1.0.0
     * @apiGroup Token
     * @apiName  CreateToken
     * @apiDescription Creates and returns new authentication token
     * @apiParam {String} email      User's email
     * @apiParam {String} password   User's password
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
        $model = new LoginForm();

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->authenticate()) {
            \Yii::$app->response->setStatusCode(201);
            return $model->authToken;
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    /**
     * @api {delete} /tokens Delete token
     * @apiVersion 1.0.0
     * @apiGroup Token
     * @apiName  DeleteToken
     * @apiDescription Deletes current user's authentication token.
     * @apiPermission Authenticated user
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 204 No content
     */
    public function actionDelete()
    {
        \Yii::$app->response->setStatusCode(204);

        UserToken::deleteAll([
            'user_id' => \Yii::$app->user->id,
            'code' => \Yii::$app->request->getAuthUser(),
        ]);
    }

    public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}