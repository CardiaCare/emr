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
use yii\filters\AccessControl;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * Description of BloodPressureController
 *
 * @author Yulia Zavyalova
 */
class BloodPressureController extends RestController
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
                'authMethods' => [
                    HttpBasicAuth::class,
                ],
            ],
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => [User::ROLE_PATIENT],
                    ],
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                ],
            ],
        ];
    }

    public function actionCreate()
    {

    }

    public function actionIndex()
    {
        return BloodPressure::find()->byPatientId(\Yii::$app->user->identity->patient->id)->all();
    }


    public function actionView($id)
    {
        $model = BloodPressure::find()->byId($id)->byPatientId(\Yii::$app->user->identity->patient->id)->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function actionDelete($id)
    {
        $model = BloodPressure::find()
            ->byId($id)
            ->one();

        $model->delete();
        
        \Yii::$app->response->setStatusCode(204);
    }
}
