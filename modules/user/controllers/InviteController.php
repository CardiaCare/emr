<?php

namespace app\modules\user\controllers;

use app\controllers\RestController;
use app\modules\user\models\UserInvite;
use app\rbac\Permissions;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\ConflictHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Controller for managing invites.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class InviteController extends RestController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'only' => ['create', 'update', 'index', 'view', 'delete'],
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
                        'actions' => ['create', 'update', 'index', 'view', 'delete'],
                        'roles' => [Permissions::INVITE_USERS]
                    ]
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'delete' => ['delete'],
                    'options' => ['options'],
                ],
            ],
        ];
    }

    /**
     * @api {post} /invites Create invite
     * @apiVersion 1.0.0
     * @apiGroup Invite
     * @apiName  CreateInvite
     * @apiDescription Creates new invite code and returns it
     * @apiParam {String} email User's email
     * @apiPermission Doctor|Chief
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 201 Created
     *     {
     *       "code": "f6asd54f98asd74f6vs6df54sdfg"
     *     }
     * @apiErrorExample {json} Validation Error:
     *     HTTP/1.1 422 Unprocessable Entity
     *     {
     *         "errors": {
     *             "email": ["First error", "Second error"],
     *         }
     *     }
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
     */
    public function actionCreate()
    {
        $model = new UserInvite([
            'referrer_id' => \Yii::$app->user->id,
            'scenario' => UserInvite::SCENARIO_DEFAULT,
        ]);

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->create()) {
            \Yii::$app->response->setStatusCode(201);
            \Yii::$app->response->getHeaders()->set('Location', Url::to(['view', 'id' => $model->id], true));
            return $model->code;
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    /**
     * @api {get} /invites Get all invites
     * @apiVersion 1.0.0
     * @apiGroup Invite
     * @apiName  GetAllInvites
     * @apiDescription Shows all invites created by current user
     * @apiPermission Doctor|Chief
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": "1",
     *              "email": "test@example.com",
     *              "created_at": "2016-05-17 18:00:00",
     *              "registered": true,
     *              "role": "patient"
     *          },
     *          {
     *              "id": "2",
     *              "email": "test2@example.com",
     *              "created_at": "2016-05-17 18:50:00",
     *              "registered": false,
     *              "role": "patient"
     *          }
     *      ]
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
     */
    public function actionIndex()
    {
        return UserInvite::find()->byReferrerId(\Yii::$app->user->id)->all();
    }

    /**
     * @api {get} /invites/{id} Get invite
     * @apiVersion 1.0.0
     * @apiGroup Invite
     * @apiName  GetInvite
     * @apiDescription Shows invite
     * @apiPermission Doctor|Chief
     * @apiParam {Integer} id Invite's id
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *
     *      {
     *          "id": "1",
     *          "email": "test@example.com",
     *          "created_at": "2016-05-17 18:00:00",
     *          "registered": true,
     *          "role": "patient"
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
     */
    public function actionView(int $id)
    {
        $invite = UserInvite::find()->byId($id)->byReferrerId(\Yii::$app->user->id)->one();

        if ($invite === null) {
            throw new NotFoundHttpException('Invite is not found');
        }

        return $invite;
    }
    
    
    /**
     * @api {delete} /invites/{id} Delete invite 
     * @apiVersion 1.0.0
     * @apiGroup Invite
     * @apiName  DeleteInvite
     * @apiDescription Delete invite without registration
     * @apiPermission Doctor|Chief
     * @apiParam {Integer} id Invite's id
     * @apiErrorExample {json} Forbidden
     *      HTTP/1.1 409 Forbidden
     *      {
     *          "name":"Forbidden",
     *          "message":"Invite is already registered",
     *          "code":0,
     *          "status":409
     *      }
     */

    public function actionDelete(int $id)
    {
        $invite = UserInvite::find()->byId($id)->byReferrerId(\Yii::$app->user->id)->one();

        if ($invite === null) {
            throw new NotFoundHttpException('Invite is not found');
        }
        $invite = $invite->toArray();
        if ($invite['registered'] !== false) {
            throw new ConflictHttpException('Invite is already registered');
        }
        UserInvite::deleteAll([
            'id' => $invite['id']
        ]);

        return \Yii::$app->response->setStatusCode(204);
    }

    public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}
