<?php

namespace app\modules\survey\controllers;

use app\controllers\RestController;
use app\modules\survey\models\Feedback;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class FeedbackController
 * @package app\modules\survey\controllers
 * @author Nikolai Lebedev
 */
class FeedbackController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'only' => ['update', 'index', 'view', 'delete', 'create'],
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
                        'actions' => ['create', 'delete'],
                        'roles' => [User::ROLE_PATIENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'index'],
                        'roles' => [User::ROLE_PATIENT, User::ROLE_DOCTOR],
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
     * @api {post} /patients/[patientid]/feedback Upload Feedback
     * @apiVersion 1.0.0
     * @apiGroup Feedback
     * @apiName  CreateFeedback
     * @apiDescription Uploads Feedback
     * @apiParam {Integer} [patientid] Patient's id
     * @apiParamExample {json} Request-Example:
     *     {
     *          "id":1,
     *          "patient_id":1,
     *          "questionnaire_id":120,
     *          "data":"json with feedbeck",
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
        $model = new Feedback();

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
     * @api {get} /patients/[patientid]/feedback Get Feedback
     * @apiVersion 1.0.0
     * @apiGroup Feedback
     * @apiName  GetFeedback
     * @apiDescription Shows uploaded Feedbacks by curent patients
     * @apiPermission Doctor|Patient
     * @apiParam {Integer} [patientid] Patient's id
     * @apiSuccessExample {json} All Feedbacks:
     *      HTTP/1.1 200 OK
     *      [
     *     {
     *          "id":1,
     *          "patient_id":1,
     *          "questionnaire_id":120,
     *          "data":"json with feedbeck",
     *          "created_at":2017-01-01
     *     },
     *     {
     *          "id":2,
     *          "patient_id":4,
     *          "questionnaire_id":120,
     *          "data":"json with feedbeck",
     *          "created_at":2017-01-01
     *     }
     *      ]
     * @apiSuccessExample {json} Single patient:
     *      HTTP/1.1 200 OK
     *      [
     *     {
     *          "id":1,
     *          "patient_id":1,
     *          "questionnaire_id":120,
     *          "data":"json with feedbeck",
     *          "created_at":2017-01-01
     *     }
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
        return Feedback::find()->byPatientId($patientid)->all();
    }
    
    
    /**
     * @api {get} /patients/{patientid}/Feedback/{id} View Feedback's information
     * @apiVersion 1.0.0
     * @apiGroup Feedback
     * @apiName  ViewFeedback
     * @apiDescription Shows Feedback information
     * @apiPermission Doctor|Patient
     * @apiParam {Integer} [patientid] Patient's id
     * @apiParam {Integer} [id] Feedback's id
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
        $model = Feedback::find()
            ->byId($id)
            ->byPatientId($patientid)
            ->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    
    /**
     * Detach Feedback
     * @param $id
     * @throws \yii\db\Exception
     */

    public function actionDelete($patientid, $id)
    {
        $model = Feedback::find()
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