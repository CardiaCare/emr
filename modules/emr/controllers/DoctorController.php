<?php

namespace app\modules\emr\controllers;

use app\controllers\RestController;
use app\modules\emr\models\Doctor;
use app\modules\user\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;


/**
 * Controller for managing doctors.
 *
 * @author Yuliya Zavyalova      <yzavyalo@cs.karelia.ru>
 */
class DoctorController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'only' => ['create','update', 'index', 'view', 'delete'],
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                    'update' => ['put'],
                    'view' => ['get'],
                    'options' => ['options'],
                    'delete' => ['delete'],
                ],
            ],
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => [User::ROLE_DOCTOR, User::ROLE_PATIENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['options'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'index', 'view', 'delete'],
                        'roles' => [User::ROLE_DOCTOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @api {put} /doctors/{id} Update doctor
     * @apiVersion 1.0.0
     * @apiGroup Doctor
     * @apiName  UpdateDoctor
     * @apiDescription Updates patient information
     * @apiPermission Doctor
     * @apiParam {String} [name]       Doctor's name
     * @apiParam {String} [patronymic] Doctor's patronymic
     * @apiParam {String} [surname]    Doctor's surname
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 204 No Content
    
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
    public function actionUpdate($id)
    {
        $model = Doctor::find()->byId($id)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->save()) {
            \Yii::$app->response->setStatusCode(204);
            return null;
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    /**
     * @api {get} /doctors View all doctors
     * @apiVersion 1.0.0
     * @apiGroup Doctor
     * @apiName  ViewAllDoctors
     * @apiDescription Shows all patients information
     * @apiPermission Doctor
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": 1,
     *              "name": "Petr",
     *              "patronymic": "Petrovich,
     *              "surname": "Petrov",
     *          },
     *          {
     *              "id": 2,
     *              "name": "Petr",
     *              "patronymic": "Petrovich,
     *              "surname": "Petrov",
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
        return (new ActiveDataProvider([
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'query' => Doctor::find(),
        ]))->getModels();
    }

    /**
     * @api {get} /doctors/{id} View doctor's information
     * @apiVersion 1.0.0
     * @apiGroup Doctor
     * @apiName  ViewDoctor
     * @apiDescription Shows patient information
     * @apiParam {Integer} [id] Doctor's id
     * @apiPermission Doctor
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "id": 1,
     *          "name": "Petr",
     *          "patronymic": "Petrovich,
     *          "surname": "Petrov"
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
        $model = Doctor::find()->byId($id)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * Detach Doctor
     * @param $id
     * @throws \yii\db\Exception
     */
    public function actionDelete($id)
    {
        $model = Doctor::find()
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

