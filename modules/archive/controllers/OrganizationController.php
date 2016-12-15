<?php

namespace app\modules\archive\controllers;

use app\controllers\RestController;
use app\modules\archive\models\OrganizationArchive;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;

/**
 * Controller for managing archive organizations.
 *
 * @author Daniil Ilin <daniil.ilin@gmail.com>
 */
class OrganizationController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class'       => CompositeAuth::class,
                'only'        => ['index', 'view', 'options'],
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'verbFilter' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'view'   => ['get'],
                    'index'  => ['get'],
                     'options' => ['options'],
                ],
            ],
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [User::ROLE_CHIEF],
                    ],
                ],
            ],
        ];
    }

    /**
     * @api {get} /archive/organizations View organization archive
     * @apiVersion 1.0.0
     * @apiGroup Archive
     * @apiName  ViewOrganizations
     * @apiDescription Shows organization archive
     * @apiPermission Chief
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": 1,
     *              "revision": 1,
     *              "action": "create",
     *              "dt_datetime": "2016-05-24 22:11",
     *              "code": "123654789",
     *              "name": "Organization name",
     *              "address": "Address",
     *              "attestat_number": "123465,
     *              "chief_name": "John Doe",
     *              "chief_position_name": "Position name",
     *              "chief_phone": "+79210101010",
     *              "chief_email": "chief@example.com",
     *          },
     *          {
     *              "id": 1,
     *              "revision": 2,
     *              "action": "update",
     *              "dt_datetime": "2016-05-24 22:11",
     *              "code": "123654789",
     *              "name": "Organization name",
     *              "address": "Address",
     *              "attestat_number": "123465,
     *              "chief_name": "John Doe",
     *              "chief_position_name": "Position name",
     *              "chief_phone": "+79210101010",
     *              "chief_email": "chief@example.com",
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
        return OrganizationArchive::find()->byOwnerId(\Yii::$app->user->id)->all();
    }

    /**
     * @api {get} /archive/organizations/{id}/{revision} View organization revision
     * @apiVersion 1.0.0
     * @apiGroup Archive
     * @apiName  ViewOrganizationRevision
     * @apiDescription Shows organization revision
     * @apiPermission Chief
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "id": 1,
     *          "revision": 1,
     *          "action": "create",
     *          "dt_datetime": "2016-05-24 22:11",
     *          "code": "123654789",
     *          "name": "Organization name",
     *          "address": "Address",
     *          "attestat_number": "123465,
     *          "chief_name": "John Doe",
     *          "chief_position_name": "Position name",
     *          "chief_phone": "+79210101010",
     *          "chief_email": "chief@example.com",
     *      },
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
    public function actionView($id, $revision)
    {
        $model = OrganizationArchive::find()->byId($id)->byOwnerId(\Yii::$app->user->id)->byRevision($revision)->all();

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