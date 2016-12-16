<?php

namespace app\modules\emr\controllers;

use app\controllers\RestController;
use app\modules\emr\models\Patient;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;


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
                'class'       => CompositeAuth::class,
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'verbFilter' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'index'  => ['get'],
                    'update' => ['put'],
                    'view'   => ['get'],
                     'options' => ['options'],
                    'delete' => ['delete'],
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
     * @api {put} /patients/{id} Update patient
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  UpdatePatient
     * @apiDescription Updates patient information
     * @apiPermission Doctor
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
     *              "patronymic": "Petrovich,
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
     *              "patronymic": "Petrovich,
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
        return Patient::find()->byDoctorId(\Yii::$app->user->identity->doctor->id)->all();
    }

    /**
     * @api {get} /patients/{id} View patient's information
     * @apiVersion 1.0.0
     * @apiGroup Patient
     * @apiName  ViewPatient
     * @apiDescription Shows patient information
     * @apiPermission Doctor
     * @apiSuccessExample {json} Success-Response:
     *      HTTP/1.1 200 OK
     *      {
     *          "id": 1,
     *          "snils": "123-111-565 22",
     *          "inn": "112263645489",
     *          "name": "Petr",
     *          "patronymic": "Petrovich,
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
        $model = Patient::find()->byId($id)->byDoctorId(\Yii::$app->user->identity->doctor->id)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    /**
     * Detach patient from doctor
     * @param $id
     * @throws \yii\db\Exception
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
}