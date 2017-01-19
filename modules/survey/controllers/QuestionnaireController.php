<?php

namespace app\modules\survey\controllers;

use app\controllers\RestController;
use app\modules\survey\models\Questionnaire;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

/**
 * Class QuestionnaireController
 * @package app\modules\survey\controllers
 * @author Nikolai Lebedev
 */
class QuestionnaireController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'only' => ['update', 'index', 'view', 'delete'],
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
                        'actions' => ['index', 'view'],
                        'roles' => [User::ROLE_PATIENT],
                    ] ,
                    [
                        'allow' => true,
                        'actions' => ['create', 'delete', 'index', 'view'],
                        'roles' => [User::ROLE_DOCTOR],
                    ]  
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'index' => ['get'],
                    'view' => ['get'],
                    'options' => ['options'],
                    'delete' => ['delete']
                ],
            ],
        ];
    }
    
    /**
     * @api {get} /survey View all Questionnairies
     * @apiVersion 1.0.0
     * @apiGroup Questionnair
     * @apiName  ViewAllQuestionnair
     * @apiDescription Shows all Questionnairies information
     * @apiPermission Doctor|Patient
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": 1,
     *              "doctor_id": "1",
     *              "data": "json with questionnaire",
     *              "version": "1.0.0",
     *          },
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "data": "json with questionnaire",
     *              "version": "1.0.1",
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
        return Questionnaire::find()
            ->byDoctorId(\Yii::$app->user->identity->doctor->id)
            ->all();
    }
    
    /**
     * @api {get} /servey/{id} View questionnaire's information
     * @apiVersion 1.0.0
     * @apiGroup Questionnair
     * @apiName  ViewQuestionnair
     * @apiDescription Shows questionnaire information
     * @apiParam {Integer} [id] Serey's id
     * @apiPermission Doctor|Patient
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "data": "json with questionnaire",
     *              "version": "1.0.1",
     *          }
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
        $model = Questionnaire::find()
            ->byId($id)
            ->byDoctorId(\Yii::$app->user->identity->doctor->id)
            ->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }
    
    
            /**
     * @api {post} /survey Upload questionnaire
     * @apiVersion 1.0.0
     * @apiGroup Questionnair
     * @apiName  CreateQuestionnair
     * @apiDescription Uploads questionnaire
     * @apiParamExample {json} Request-Example:
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "data": "json with questionnaire",
     *              "version": "1.0.1",
     *          }
     * @apiPermission Doctor
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
        $model = new Questionnaire();

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
     * Detach questionnaire
     * @param $id
     * @throws \yii\db\Exception
     */

    public function actionDelete($id)
    {
        $model = Questionnaire::find()
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