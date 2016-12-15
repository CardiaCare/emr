<?php

namespace app\modules\emr\controllers;

use app\controllers\RestController;
use app\modules\emr\models\Test;
use app\modules\user\models\User;
use yii\web\ServerErrorHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;

/**
 * Controller for uploading test results.
 * 
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class TestController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class'       => CompositeAuth::class,
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'   => true,
                        'actions' => ['create', 'options'],
                        'roles'   => [User::ROLE_PATIENT],
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['index', 'options'],
                        'roles'   => [User::ROLE_DOCTOR],
                    ],
                ],
            ],
            'verbFilter' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'options' => ['options'],
                    'index'  => ['get'],
                ],
            ],
        ];
    }

    /**
     * @api {post} /tests Upload test data
     * @apiVersion 1.0.0
     * @apiGroup Test
     * @apiName  CreateTest
     * @apiDescription Uploads test data
     * @apiParamExample {json} Request-Example:
     *     {
     *       "question1": "1",
     *       "question2": "2",
     *       "question3": "3"
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
        $model = new Test([
            'data' => \Yii::$app->getRequest()->getBodyParams(),
        ]);

        if ($model->save()) {
            \Yii::$app->response->setStatusCode(201);
            return null;
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }

    /**
     * @api {get} /tests/[id] Get tests
     * @apiVersion 1.0.0
     * @apiGroup Test
     * @apiName  GetTest
     * @apiDescription Shows uploaded tests by all patients (or if patient's id is specified by only that patient)
     * @apiPermission Doctor
     * @apiParam {Integer} [id] Patient's id
     * @apiSuccessExample {json} All patients:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": "1",
     *              "patient_id": "1",
     *              "created_at": "2016-05-17 18:00:00",
     *              "data": {
     *                  "question1": "1",
     *                  "question2": "2",
     *                  "question3": "3"
     *              }
     *          },
     *          {
     *              "id": "2",
     *              "patient_id": "2",
     *              "created_at": "2016-05-17 19:00:00",
     *              "data": {
     *                  "question1": "3",
     *                  "question2": "4",
     *                  "question3": "5"
     *              }
     *          }
     *      ]
     * @apiSuccessExample {json} Single patient:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": "1",
     *              "patient_id": "1",
     *              "created_at": "2016-05-17 18:00:00",
     *              "data": {
     *                  "question1": "1",
     *                  "question2": "2",
     *                  "question3": "3"
     *              }
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
    public function actionIndex($id = null)
    {
        $query = Test::find()->byDoctorId(\Yii::$app->user->identity->doctor->id);
        
        if ($id !== null) {
            $query->byPatientId($id);
        }
        
        return $query->all();
    }
    
        public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}