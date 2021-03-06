<?php

namespace app\modules\survey_v2\controllers;

use app\controllers\RestController;
use app\modules\survey_v2\models\Converter\FeedbackToArrayConverter;
use app\modules\survey_v2\models\Feedback;
use app\modules\survey_v2\models\ResponseFile;
use app\modules\user\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\auth\CompositeAuth;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UploadedFile;

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
                'only' => ['create', 'update', 'index', 'view', 'delete', 'upload-response-file'],
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
                        'actions' => ['create', 'delete', 'upload-response-file'],
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
     * @api {post} /feedback Upload Feedback
     * @apiVersion 1.0.0
     * @apiGroup Feedback
     * @apiName  CreateFeedback
     * @apiDescription Uploads Feedback
     * @apiParamExample {json} Request-Example:
     *     {
     *          "questionnaire_id": 120,
     *          "responds": []
     *     }
     * @apiPermission Patient
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 201 Created
     *      {
     *          "questionnaire_id": "101",
     *          "created_at": "2017-02-12",
     *          "patient_id": 2,
     *          "id": 19
     *      }
     * @apiErrorExample {json} Unauthorized
     *      HTTP/1.1 401 Unauthorized
     *      {
     *          "name": "Unauthorized",
     *          "message": "You are requesting with an invalid credential.",
     *          "code": 0,
     *          "status": 401
     *      }
     */
    public function actionCreate()
    {
        $model = new Feedback([
            '_responds' => \Yii::$app->getRequest()->getBodyParam('responds'),
        ]);

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        $transaction = Feedback::getDb()->beginTransaction();

        try {
            if ($model->save()) {
                \Yii::$app->response->setStatusCode(201);
                $transaction->commit();

                return $model;
            } elseif ($model->hasErrors()) {
                \Yii::$app->response->setStatusCode(422);

                return ['errors' => $model->getErrors()];
            } else {
                throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
            }
        } catch(\Exception $e) {
            $transaction->rollBack();

            throw $e;
        } catch(\Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }
    }

    /**
     * @api {get} /patients/[patientid]/feedback Get Feedback
     * @apiVersion 1.0.0
     * @apiGroup Feedback
     * @apiName  GetFeedback
     * @apiDescription Shows uploaded Feedbacks by current patients
     * @apiPermission Doctor|Patient
     * @apiParam {Integer} [patientid] Patient's id
     * @apiSuccessExample {json} All Feedbacks:
     *      HTTP/1.1 200 OK
     *      [
     *     {
     *          "id": 1,
     *          "patient_id": 1,
     *          "questionnaire_id": 120,
     *          "created_at": "2017-02-12",
     *     },
     *     {
     *          "id": 2,
     *          "patient_id": 4,
     *          "questionnaire_id": 120,
     *          "created_at": "2017-02-12",
     *     }
     *      ]
     * @apiSuccessExample {json} Single patient:
     *      HTTP/1.1 200 OK
     *      [
     *     {
     *          "id": 1,
     *          "patient_id": 1,
     *          "questionnaire_id": 120,
     *          "created_at": 2017-01-01
     *     }
     *      ]
     * @apiErrorExample {json} Unauthorized
     *      HTTP/1.1 401 Unauthorized
     *      {
     *          "name": "Unauthorized",
     *          "message": "You are requesting with an invalid credential.",
     *          "code": 0,
     *          "status": 401
     *      }
     * @apiErrorExample {json} Forbidden
     *      HTTP/1.1 403 Forbidden
     *      {
     *          "name": "Forbidden",
     *          "message": "You are not allowed to perform this action.",
     *          "code": 0,
     *          "status": 403
     *      }
     */
    public function actionIndex($patientid = null)
    {
        if (is_null($patientid)) {
            $query = Feedback::find();
        } else {
            $query = Feedback::find()->byPatientId($patientid);
        }

        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'pageSize' => 10,
            ],
            'query' => $query->orderBy([
                'created_at' => SORT_DESC,
            ]),
        ]);

        $dataProvider->prepare();

        $this->setPaginationHeaders($dataProvider);

        return $dataProvider->getModels();
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
     *          "id": 1,
     *          "patient_id": 2,
     *          "questionnaire_id": 120,
     *          "created_at": 2017-01-01,
     *          "responds": []
     *
     *     }
     * @apiErrorExample {json} Unauthorized
     *      HTTP/1.1 401 Unauthorized
     *      {
     *          "name": "Unauthorized",
     *          "message": "You are requesting with an invalid credential.",
     *          "code": 0,
     *          "status": 401
     *      }
     * @apiErrorExample {json} Forbidden
     *      HTTP/1.1 403 Forbidden
     *      {
     *          "name": "Forbidden",
     *          "message": "You are not allowed to perform this action.",
     *          "code": 0,
     *          "status": 403
     *      }
     * @apiErrorExample {json} Not found
     *      HTTP/1.1 404 Not found
     *      {
     *          "name": "Not found",
     *          "message": "Not found",
     *          "code": 0,
     *          "status": 404
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

        return $this->getFeedbackToArrayConverter()->convert($model);
    }

    /**
     * @api {delete} /feedback/{id} Delete feedback
     * @apiVersion 1.0.0
     * @apiGroup Feedback
     * @apiName  DeleteFeedback
     * @apiDescription Deletes feedback
     * @apiPermission Patient
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
    public function actionDelete($id)
    {
        $model = Feedback::find()
            ->byId($id)
            ->one();
        $model->delete();

        \Yii::$app->response->setStatusCode(204);
    }

    /**
     * @api {post} /feedback/response-file Upload ResponseFile
     * @apiVersion 1.0.0
     * @apiGroup Feedback
     * @apiName  UploadResponseFile
     * @apiDescription Uploads response file
     * @apiParam {File} file Response file
     * @apiPermission Patient
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 201 Created
     *      {
     *          "url": "https://domain/uploads/file.ext"
     *      }
     * @apiErrorExample {json} Unauthorized
     *      HTTP/1.1 401 Unauthorized
     *      {
     *          "name":"Unauthorized",
     *          "message":"You are requesting with an invalid credential.",
     *          "code":0,
     *          "status":401
     *      }
     */
    public function actionUploadResponseFile()
    {
        $model = new ResponseFile();
        $model->uploadedFile = UploadedFile::getInstanceByName('file');

        if (!($model->uploadedFile instanceof UploadedFile)) {
            throw new BadRequestHttpException();
        }

        if ($model->upload('uploads/patient'.\Yii::$app->user->identity->patient->id.'/')) {
            \Yii::$app->response->setStatusCode(201);

            return [
                'url' => $model->url,
            ];
        } else {
            throw new ServerErrorHttpException('Failed to upload response file for unknown reason.');
        }
    }

    public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }

    /**
     * @return FeedbackToArrayConverter
     */
    private function getFeedbackToArrayConverter()
    {
        return new FeedbackToArrayConverter();
    }

    /**
     * @param ActiveDataProvider $dataProvider
     */
    private function setPaginationHeaders(ActiveDataProvider $dataProvider)
    {
        $headers = \Yii::$app->response->headers;

        $headers->add('X-Pagination-Total-Count', $dataProvider->getTotalCount());
        $headers->add('X-Pagination-Page-Count', $dataProvider->getPagination()->getPageCount());
        $headers->add('X-Pagination-Current-Page', $dataProvider->getPagination()->getPage() + 1);
        $headers->add('X-Pagination-Per-Page', $dataProvider->getCount());
    }
}
