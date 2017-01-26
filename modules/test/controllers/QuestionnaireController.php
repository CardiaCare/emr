<?php

namespace app\modules\test\controllers;

use app\controllers\RestController;
use app\modules\test\models\Questionnaire;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;

class QuestionnaireController extends RestController
{
    public function behaviors()
    {
        return [
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'view' => ['get'],
                ],
            ],
        ];
    }

    public function actionView($id)
    {
        $model = Questionnaire::find()
            ->byId($id)
            ->one();

        if ($model == null) {
            throw new NotFoundHttpException();
        }

        return $model;
    }

    public function actionCreate()
    {
        $model = new Questionnaire([
            '_data' => \Yii::$app->getRequest()->getBodyParam('data'),
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
}
