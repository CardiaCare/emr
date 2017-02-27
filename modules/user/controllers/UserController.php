<?php

namespace app\modules\user\controllers;

use app\controllers\RestController;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
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
    
        /**
     * @api {get} /users/{id} View User's information
     * @apiVersion 1.0.0
     * @apiGroup User
     * @apiName  ViewUser
     * @apiDescription Shows User information
     * @apiParam {Integer} [id] User's id
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "id": 1,
     *          "email": "Petr@mail.com",
     *          "inviteCode": "8264855,
     *      }
     * @apiErrorExample {json} Unauthorized
     *      HTTP/1.1 401 Unauthorized
     *      {
     *          "name":"Unauthorized",
     *          "message":"You are requesting with an invalid credential.",
     *          "code":0,
     *          "status":401
     *      }
     * @apiErrorExample {json} Forbidden
     *      HTTP/1.1 403 Forbidden
     *      {
     *          "name":"Forbidden",
     *          "message":"You are not allowed to perform this action.",
     *          "code":0,
     *          "status":403
     *      }
     * @apiErrorExample {json} Not found
     *      HTTP/1.1 404 Not found
     *      {
     *          "name":"Not found",
     *          "message":"Not found",
     *          "code":0,
     *          "status":404
     *      }
     */

    public function actionView($id)
    {
        if($id != \Yii::$app->user->identity->id){
            throw new ForbiddenHttpException();
        }

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