<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace app\modules\emr\controllers;

use app\controllers\RestController;
use app\modules\emr\models\BloodPressure;
use app\modules\user\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Description of BloodPressureController
 *
 * @author Yulia Zavyalova
 */
class BloodpressureController extends RestController
{
    //put your code here

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
                        'actions' => ['create'],
                        'roles' => [User::ROLE_PATIENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'index', 'view', 'delete'],
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'view' => ['get'],
                    'index' => ['get'],
                    'options' => ['options'],
                    'delete' => ['delete']
                ],
            ],
        ];
    }
    
    /**
     * @api {post} /patients/[patientid]/bloodpressure Upload bloodpressure
     * @apiVersion 1.0.0
     * @apiGroup Bloodpressure
     * @apiName  CreateBloodpressure
     * @apiDescription Uploads bloodpressure
     * @apiParam {Integer} [patientid] Patient's id
     * @apiParamExample {json} Request-Example:
     *     {
     *          "id":1,
     *          "patient_id":2,
     *          "systolic":120,
     *          "diastolic":80,
     *          "created_at":2017-01-01
     *     }
     * @apiPermission Patient
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 201 Created
     * @apiErrorExample {json} Unauthorized
     *      HTTP/1.1 401 Unauthorized
     *      {
     *          "name":"Unauthorized",
     *          "message":"You are requesting with an invalid credential.",
     *          "code":0,
     *          "status":401
     *      }
     */

    public function actionCreate()
    {
        $model = new BloodPressure();

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
    
    /**
     * @api {get} /patients/[patientid]/bloodpressure Get bloodpressure
     * @apiVersion 1.0.0
     * @apiGroup Bloodpressure
     * @apiName  GetBloodpressure
     * @apiDescription Shows uploaded bloodpressures by curent patients
     * @apiPermission Doctor
     * @apiParam {Integer} [patientid] Patient's id
     * @apiSuccessExample {json} All patients:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id":1,
     *              "patient_id":2,
     *              "systolic":120,
     *              "diastolic":80,
     *              "created_at":2017-01-01
     *          },
     *          {
     *              "id":2,
     *              "patient_id":4,
     *              "systolic":110,
     *              "diastolic":70,
     *              "created_at":2017-01-01
     *          }
     *      ]
     * @apiSuccessExample {json} Single patient:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id":1,
     *              "patient_id":2,
     *              "systolic":120,
     *              "diastolic":80,
     *              "created_at":2017-01-01
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

    public function actionIndex($patientid)
    {
        return (new ActiveDataProvider([
            'pagination' => [
                'defaultPageSize' => 10,
            ],
            'query' => BloodPressure::find()->byPatientId($patientid),
        ]))->getModels();
    }

    /**
     * @api {get} /patients/{patientid}/bloodpressure/{id} View bloodpressure's information
     * @apiVersion 1.0.0
     * @apiGroup Bloodpressure
     * @apiName  ViewBloodpressure
     * @apiDescription Shows bloodpressure information
     * @apiPermission Doctor
     * @apiParam {Integer} [patientid] Patient's id
     * @apiParam {Integer} [id] Bloodpressure's id
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *     {
     *          "id":1,
     *          "patient_id":2,
     *          "systolic":120,
     *          "diastolic":80,
     *          "created_at":2017-01-01
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
    
    public function actionView($patientid, $id)
    {
        $model = BloodPressure::find()->byId($id)->byPatientId($patientid)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
    
    
    /**
     * @api {delete} /patients/{patientid}/bloodpressure/{id} Delete bloodpressure
     * @apiVersion 1.0.0
     * @apiGroup Bloodpressure
     * @apiName  DeleteBloodpressure
     * @apiDescription Deletes Bloodpressure
     * @apiParam {Integer} [patientid] Patient's id
     * @apiParam {Integer} [id] Bloodpressure's id
     * @apiPermission Doctor|Patient
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 404 Not found
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
    public function actionDelete($patientid, $id)
    {
        $model = BloodPressure::find()
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
