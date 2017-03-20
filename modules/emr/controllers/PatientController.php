<?php

namespace app\modules\emr\controllers;

use app\controllers\RestController;
use app\modules\emr\models\Patient;
use app\modules\survey\models\Questionnaire;
use app\modules\user\models\User;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\ConflictHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;


/**
 * Controller for managing patients.
 *
 * @author Daniil Ilin      <daniil.ilin@gmail.com>
 * @author Zykova Ekaterina <katyazkv15@mail.ru>
 * @author Dmitry Erofeev   <dmeroff@gmail.com>
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
                'class' => CompositeAuth::class,
                'only' => [
                    'update',
                    'index',
                    'view',
                    'delete',
                    'addquestionnaire',
                    'removequestionnaire',
                    'questionnaires',
                    'doctors'
                ],
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
                    'addquestionnaire' => ['post'],
                    'removequestionnaire' => ['delete'],
                    'questionnaires' => ['get'],
                    'doctors' => ['get']
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
                        'actions' => ['update', 'index', 'view', 'questionnaires', 'doctors'],
                        'roles' => [User::ROLE_PATIENT],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'update',
                            'index',
                            'view',
                            'delete',
                            'addquestionnaire',
                            'removequestionnaire',
                            'questionnaires',
                            'doctors',
                        ],
                        'roles' => [User::ROLE_DOCTOR],
                    ],
                ],
            ],
        ];
    }

    /**
     * @api {put} /patients/{id} Update patient
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  UpdatePatient
     * @apiDescription Updates patient information
     * @apiPermission Doctor|Patient
     * @apiParam {String} [snils]      Patient's snils
     * @apiParam {String} [inn]        Patient's inn
     * @apiParam {String} [name]       Patient's name
     * @apiParam {String} [patronymic] Patient's patronymic
     * @apiParam {String} [surname]    Patient's surname
     * @apiParam {String} [birthday]   Patient's birthday
     * @apiParam {String} [birthplace] Patient's birthplace
     * @apiParam {String} [gender]     Patient's gender
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 204 No Content
     * @apiErrorExample {json} Validation Error:
     *     HTTP/1.1 422 Unprocessable Entity
     *     {
     *         "errors": {
     *             "snils": ["First error"]
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
    public function actionUpdate($id)
    {
        $model = Patient::find()->byId($id)->byDoctorId(\Yii::$app->user->identity->doctor->id)->one();

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
     * @api {get} /patients/{id} View all patients
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  ViewAllPatients
     * @apiParam {Integer} [id] Patient's id
     * @apiDescription Shows all patients information
     * @apiPermission Doctor
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      [
     *          {
     *              "id": 1,
     *              "snils": "123-111-565 22",
     *              "inn": "112263645489",
     *              "name": "Petr",
     *              "patronymic": "Petrovich",
     *              "surname": "Petrov",
     *              "birthday": "1995-01-01",
     *              "birthplace": "Birth place",
     *              "gender": 0,
     *          },
     *          {
     *              "id": 2,
     *              "snils": "123-111-565 22",
     *              "inn": "112263645489",
     *              "name": "Petr",
     *              "patronymic": "Petrovich",
     *              "surname": "Petrov",
     *              "birthday": "1995-01-01",
     *              "birthplace": "Birth place",
     *              "gender": 0,
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
        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'pageSize' => 10,
            ],
            'query' => Patient::find()->byDoctorId(\Yii::$app->user->identity->doctor->id),
        ]);

        $dataProvider->prepare();

        $this->setPaginationHeaders($dataProvider);

        return $dataProvider->getModels();
    }

    /**
     * @api {get} /patients/{id} View patient's information
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  ViewPatient
     * @apiParam {Integer} [id] Patient's id
     * @apiDescription Shows patient information
     * @apiPermission Doctor|Patient
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "id": 1,
     *          "snils": "123-111-565 22",
     *          "inn": "112263645489",
     *          "name": "Petr",
     *          "patronymic": "Petrovich",
     *          "surname": "Petrov",
     *          "birthday": "1995-01-01",
     *          "birthplace": "Birth place",
     *          "gender": 0,
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
        $model = Patient::find()->byId($id)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * @api {get} /patients/{id}/doctors View patient's doctors
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  Viewdoctors
     * @apiParam {Integer} [id] Patient's id
     * @apiDescription Shows patient doctors
     * @apiPermission Patient
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
*[
*  {
*   "id": 2,
*    "user_id": 12,
*    "organization_id": 3,
*    "name": "Jon",
*    "patronymic": NULL,
*    "surname": "Smith",
*    "email": "smith_jon@mail.com"
*  }
*]
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
    public function actionDoctors($id)
    {
        $model = Patient::find()->byId($id)->one();

        if ($model == null) {
            throw new NotFoundHttpException("Patient $id is not found");
        }

        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'pageSize' => 10,
            ],
            'query' => $model->getDoctors(),
        ]);

        $dataProvider->prepare();

        $this->setPaginationHeaders($dataProvider);

        return $dataProvider->getModels();

    }

    /**
     * @api {get} /patients/{id}/questionnaires View patient's questionnaires
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  ViewQuestionnaires
     * @apiParam {Integer} [id] Patient's id
     * @apiDescription Shows patient questionnaires
     * @apiPermission Doctor|Patient
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
*[
 * {
 *   "id": 7,
 *   "doctor_id": 12,
 *   "version": "0.1.1",
 *   "description": "new questionnaire",
 *   "created_at": "2017-02-09",
 *   "lang": "En",
 *   "emergency": 0
*  }
*]
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
    public function actionQuestionnaires($pid)
    {
        /** @var Patient $model */
        $model = Patient::find()->byId($pid)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'pageSize' => 10,
            ],
            'query' => $model->getQuestionnaires(),
        ]);

        $dataProvider->prepare();

        $this->setPaginationHeaders($dataProvider);

        return $dataProvider->getModels();
    }
    
    
    /**
     * @api {post} /patients/{pid}/questionnaires/{qid} Link patient with questionnaire
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  CreateQuestionnaireLink
         * @apiParam {Integer} [pid] Patient's id
         * @apiParam {Integer} [qid] Questionnaire's id
     * @apiDescription Link patient with questionnaire
     * @apiPermission Doctor
     * @apiParamExample {json} Request-Example:
     *          {
     *              "version": "1.0.1",
     *              "description": "Description",
     *              "lang": "ru",
     *              "questions": []
     *          }
     * @apiPermission Doctor
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 201 Created
     *          {
     *              "id": 2,
     *              "doctor_id": "2",
     *              "version": "1.0.1",
     *              "created_at": "2016-12-31",
     *              "description": "Description",
     *              "lang": "ru"
     *          }
     * @apiErrorExample {json} Unauthorized
     *      HTTP/1.1 401 Unauthorized
     *      {
     *          "name":"Unauthorized",
     *          "message":"You are requesting with an invalid credential.",
     *          "code":0,
     *          "status":401
     *      }
     */

    public function actionAddquestionnaire($pid, $qid)
    {
        /** @var Patient $model */
        $model = Patient::find()->byId($pid)->one();

        if ($model == null) {
            throw new NotFoundHttpException("Patient $pid is not found");
        }

        $existQuesttionnaires =
            $model->getQuestionnaires()
                ->andWhere(['id' => $qid])
                ->one();

        if (!is_null($existQuesttionnaires)) {
            throw new ConflictHttpException("Questionnaire $qid has already attached to patient $pid");
        }

        $questionnarie = Questionnaire::find()->byId($qid)->one();

        if ($questionnarie == null) {
            throw new NotFoundHttpException("Questionnaire $qid is not found");
        }

        \Yii::$app->db->createCommand()
            ->insert('patients_questionnaires', ['patient_id' => $pid, 'questionnaire_id' => $qid])
            ->execute();

        \Yii::$app->response->setStatusCode(204);
    }
    
    
    /**
     * @api {delete} /patients/{pid}/questionnaires/{qid} Delete  link between patient and questionnaire
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  DeleteQuestionnaireLink
     * @apiDescription Deletes questionnaire
         * @apiParam {Integer} [pid] Patient's id
         * @apiParam {Integer} [qid] Questionnaire's id
     * @apiDescription Delete link between patient and questionnaire
     * @apiPermission Doctor
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 Deleted
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
    public function actionRemovequestionnaire($pid, $qid)
    {
        /** @var Patient $model */
        $model = Patient::find()->byId($pid)->one();

        if ($model == null) {
            throw new NotFoundHttpException("Patient $pid is not found");
        }

        $questionnarie = Questionnaire::find()->byId($qid)->one();

        if ($questionnarie == null) {
            throw new NotFoundHttpException("Questionnaire $qid is not found");
        }

        \Yii::$app->db->createCommand()
            ->delete('patients_questionnaires', ['patient_id' => $pid, 'questionnaire_id' => $qid])
            ->execute();

        \Yii::$app->response->setStatusCode(204);
    }

    /**
     * @api {delete} /patient/{id} Delete patient
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  DeletePatient
     * @apiDescription Deletes questionnaire
     * @apiPermission Doctor
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 200 Delete
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
        \Yii::$app->db->createCommand()
            ->delete('patient_to_doctor', ['patient_id' => $id, 'doctor_id' => \Yii::$app->user->identity->doctor->id])
            ->execute();

        \Yii::$app->response->setStatusCode(204);
    }

    public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
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
