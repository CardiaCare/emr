<?php

namespace app\modules\emr\controllers;

use app\controllers\RestController;
use app\modules\emr\models\Biosignal;
use app\modules\user\models\User;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\web\UploadedFile;

/**
 * Biosignal controller.
 *
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class BiosignalController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'authenticator' => [
                'class'       => CompositeAuth::class,
                'only' => ['create', 'update', 'index', 'view', 'delete'],
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
                        'actions' => ['create'],
                        'roles'   => [User::ROLE_PATIENT],
                    ]
                ],
            ],
            'verbFilter' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'create' => ['post'],
                    'options' => ['options'],
                ],
            ],
        ];
    }

    /**
     * @api {post} /biosignals Upload biosignal
     * @apiVersion 1.0.0
     * @apiGroup Biosignal
     * @apiName  CreateBiosignal
     * @apiDescription Uploads biosignal binary data
     * @apiParam {Binary} data Biosignal binary data
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
        $model = new Biosignal();
        $file  = UploadedFile::getInstanceByName('data');
        
        if (!($file instanceof UploadedFile)) {
            throw new BadRequestHttpException();
        }
        
        $model->data = file_get_contents($file->tempName);

        if ($model->save()) {
            \Yii::$app->response->setStatusCode(201);
            return null;
        } else {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }
    }
    
        public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}