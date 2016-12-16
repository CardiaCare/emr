<?php

namespace app\modules\archive\controllers;

use app\controllers\RestController;
use app\modules\archive\models\PatientArchive;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;

/**
 * Controller for managing archive patients.
 *
 * @author Daniil Ilin <daniil.ilin@gmail.com>
 */
class PatientController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class'       => CompositeAuth::class,
                'only'        => ['index', 'view'],
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
                        'roles' => [User::ROLE_DOCTOR],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['options'],
                        'roles' => ['?'],
                    ]  
                ],
            ],
        ];
    }

    /**
     * @api {get} /archive/patients View patient archive
     * @apiVersion 1.0.0
     * @apiGroup Archive
     * @apiName  ViewPatients
     * @apiDescription Shows patient archive
     * @apiPermission Doctor
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": 1,
     *              "revision": 1,
     *              "action": "create",
     *              "dt_datetime": "2016-05-24 22:11",
     *              "snils": "123-111-565 22",
     *              "inn": "112263645489",
     *              "name": "Petr",
     *              "patronymic": "Petrovich,
     *              "surname": "Petrov",
     *              "birthday": "1995-01-01",
     *              "birthplace": "Birth place",
     *          },
     *          {
     *              "id": 1,
     *              "revision": 2,
     *              "action": "update",
     *              "dt_datetime": "2016-05-24 22:11",
     *              "snils": "123-111-565 22",
     *              "inn": "112263645489",
     *              "name": "Petr",
     *              "patronymic": "Petrovich,
     *              "surname": "Petrov",
     *              "birthday": "1995-01-01",
     *              "birthplace": "Birth place",
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
        return PatientArchive::find()->byDoctorId(\Yii::$app->user->identity->doctor->id)->all();
    }

    /**
     * @api {get} /archive/patients/{id}/{revision} View patient revision
     * @apiVersion 1.0.0
     * @apiGroup Archive
     * @apiName  ViewPatientRevision
     * @apiDescription Shows patient revision
     * @apiPermission Doctor
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "id": 1,
     *          "revision": 2,
     *          "action": "update",
     *          "dt_datetime": "2016-05-24 22:11",
     *          "snils": "123-111-565 22",
     *          "inn": "112263645489",
     *          "name": "Petr",
     *          "patronymic": "Petrovich,
     *          "surname": "Petrov",
     *          "birthday": "1995-01-01",
     *          "birthplace": "Birth place",
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
        $model = PatientArchive::find()
            ->byId($id)
            ->byDoctorId(\Yii::$app->user->identity->doctor->id)
            ->byRevision($revision)
            ->all();

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