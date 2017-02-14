<?php

namespace app\modules\organization\controllers;

use app\controllers\RestController;
use app\modules\organization\models\Organization;
use app\rbac\Permissions;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;

/**
 * Controller for managing organizations.
 *
 * @author Daniil Ilin    <daniil.ilin@gmail.com>
 * @author Dmitry Erofeev <dmeroff@gmail.com>
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
                    ] ,
                    [
                        'allow' => true,
                        'roles' => [Permissions::MANAGE_ORGANIZATIONS],
                    ] 
                ],
            ],
            'verbFilter' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'view'   => ['get'],
                    'update' => ['put'],
                    'options' => ['options'],
                ],
            ],
        ];
    }

    /**
     * @api {get} /organizations View organization
     * @apiVersion 1.0.0
     * @apiGroup Organization
     * @apiName  ViewOrganization
     * @apiDescription Shows organization data
     * @apiPermission Chief
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "code": "123654789",
     *          "name": "Organization name",
     *          "address": "Address",
     *          "attestat_number": "123465,
     *          "chief_name": "John Doe",
     *          "chief_position_name": "Position name",
     *          "chief_phone": "+79210101010",
     *          "chief_email": "chief@example.com",
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
    public function actionView()
    {
        $model = \Yii::$app->user->identity->organization;

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * @api {put} /organizations Update organization
     * @apiVersion 1.0.0
     * @apiGroup Organization
     * @apiName  UpdateOrganization
     * @apiDescription Updates organization
     * @apiPermission Chief
     * @apiParam {String} [code]                Organization's code
     * @apiParam {String} [name]                Organization's name
     * @apiParam {String} [address]             Organization's address
     * @apiParam {String} [attestat_number]     Organization's attestat number
     * @apiParam {String} [chief_name]          Organization's chief name
     * @apiParam {String} [chief_position_name] Organization's chief position
     * @apiParam {String} [chief_phone]         Organization's chief phone
     * @apiParam {String} [chief_email]         Organization's chief email
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 204 No Content
     * @apiErrorExample {json} Validation Error:
     *     HTTP/1.1 422 Unprocessable Entity
     *     {
     *         "errors": {
     *             "code": ["First error", "Second error"]
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
     * @apiErrorExample {json} Not found
     *      HTTP/1.1 404 Not found
     *      {
     *          "name":"Not found",
     *          "message":"Not found",
     *          "code":0,
     *          "status":404
     *      }
     */
    public function actionUpdate()
    {
        $model = \Yii::$app->user->identity->organization;

        if ($model == null) {
            throw new NotFoundHttpException('Not found');
        }

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            \Yii::$app->response->setStatusCode(204);
            return null;
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to update organization for unknown reason.');
        }
    }
    
    public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}