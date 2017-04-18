<?php

namespace app\modules\biostats\controllers;

use app\controllers\RestController;
use app\modules\biostats\dispatcher\BiostatsDispatcher;
use app\modules\biostats\dispatcher\request\UserRequestBiostatsRequest;
use app\modules\biostats\dispatcher\request\RecordBiostatsRequest;
use app\modules\user\models\User;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;

class BiostatsController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class'       => CompositeAuth::class,
                'only' => ['index', 'view','create'],
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
                    ]  ,
                    [
                        'allow'   => true,
                        'actions' => ['index', 'view','create'],
                        'roles'   => [User::ROLE_DOCTOR, User::ROLE_PATIENT],
                    ]
                ],
            ],
            'verbFilter' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'index' => ['get'],
                    'options' => ['options'],
                ],
            ],
        ];
    }

    /**
     * @api {get} /patients/{patientId}/biostats Get Biostats
     * @apiVersion 1.0.0
     * @apiGroup Biostats
     * @apiName  GetBiostats
     * @apiDescription Shows calculated Biostats
     * @apiPermission Doctor|Patient
     * @apiSuccessExample {json} All Feedbacks:
     *      HTTP/1.1 200 OK
     *      [
     *      {
     *          "id": 1,
     *          "patient_id": 1,
     *          "data": "filedata",
     *          "created_at": "2017-02-15 08:56:41"
     *      },
     *      {
     *          "id": 2,
     *          "patient_id": 1,
     *          "data": "filedata",
     *          "created_at": "2017-02-15 08:56:41"
     *      },
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
    public function actionView($patientId = null)
    {
        $biostats = $this->getUserRequestBiostatsHandler()->dispatch(new UserRequestBiostatsRequest());

        return $biostats->serialize();
    }
    
    public function actionCreate($patientId = null)
    {
        $biostats = $this->getRecordBiostatsHandler()->dispatch(new RecordBiostatsHandler());

        return $biostats->serialize();
    }
    
    public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }

    /**
     * @return BiostatsDispatcher
     */
    private function getUserRequestBiostatsHandler()
    {
        return new BiostatsDispatcher();
    }
        private function getRecordBiostatsHandler()
    {
        return new BiostatsDispatcher();
    }
    
}
