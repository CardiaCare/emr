<?php

namespace app\modules\test\controllers;

use app\controllers\RestController;
use app\modules\test\models\Converter\QuestionnaireToArrayConverter;
use app\modules\test\models\Questionnaire;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use app\modules\user\models\User;
use yii\filters\auth\CompositeAuth;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;

class QuestionnaireController extends RestController
{
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
                    'index' => ['get'],
                    'create' => ['post'],
                    'view' => ['get'],
                    'delete' => ['delete'],
                ],
            ],
        ];
    }

    /**
     * @api {get} /questionnaire View all Questionnaires
     * @apiVersion 1.0.0
     * @apiGroup Questionnaire
     * @apiName  ViewAllQuestionnaire
     * @apiDescription Shows all Questionnaires information
     * @apiPermission Doctor|Patient
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "version": "1.0.1",
     *              "created_at": "31.12.16",
     *              "description": "Description",
     *              "lang": "ru",
     *          },
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "version": "1.0.1",
     *              "created_at": "31.12.16",
     *              "description": "Description",
     *              "lang": "ru",
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
            ->byDoctorId(\Yii::$app->user->identity->id)
            ->all();
    }

    /**
     * @api {get} /questionnaire/{id} View questionnaire's information
     * @apiVersion 1.0.0
     * @apiGroup Questionnaire
     * @apiName  ViewQuestionnaire
     * @apiDescription Shows questionnaire information
     * @apiParam {Integer} [id] Questionnaire's id
     * @apiPermission Doctor|Patient
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "version": "1.0.1",
     *              "created_at": "31.12.16",
     *              "description": "Description",
     *              "lang": "ru",
     *              "questions": []
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
            ->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $this->getQuestionnaireToArrayConverter()->convert($model);
    }

    /**
     * @api {post} /questionnaire Upload questionnaire
     * @apiVersion 1.0.0
     * @apiGroup Questionnaire
     * @apiName  CreateQuestionnaire
     * @apiDescription Uploads questionnaire
     * @apiParamExample {json} Request-Example:
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "version": "1.0.1",
     *              "created_at": "31.12.16",
     *              "description": "Description",
     *              "lang": "ru",
     *              "questions": []
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
        $model = new Questionnaire([
            '_questions' => \Yii::$app->getRequest()->getBodyParam('questions'),
        ]);

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
     * @api {delete} /questionnaire/{id} Delete questionnaire
     * @apiVersion 1.0.0
     * @apiGroup Questionnaire
     * @apiName  DeleteQuestionnaire
     * @apiDescription Deletes questionnaire
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
    public function actionDelete($id)
    {
        $model = Questionnaire::find()
            ->byId($id)
            ->one();
        $model->delete();

        \Yii::$app->response->setStatusCode(204);
    }

    /**
     * @return QuestionnaireToArrayConverter
     */
    private function getQuestionnaireToArrayConverter()
    {
        return new QuestionnaireToArrayConverter();
    }
}
