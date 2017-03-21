<?php

namespace app\modules\emr\controllers;

use app\controllers\RestController;
use app\modules\emr\models\Biosignal;
use app\modules\user\models\User;
use yii\data\ActiveDataProvider;
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
                    ],
                    [
                        'allow'   => true,
                        'actions' => ['index'],
                        'roles'   => [User::ROLE_DOCTOR, User::ROLE_PATIENT],
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
     * @api {get} /biosignals Get Biosignal
     * @apiVersion 1.0.0
     * @apiGroup Biosignal
     * @apiName  GetBiosignal
     * @apiDescription Shows uploaded Biosignals by patients
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
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'pagination' => [
                'pageSize' => 10,
            ],
            'query' => Biosignal::find()->orderBy([
                'created_at' => SORT_DESC,
            ]),
        ]);

        $dataProvider->prepare();

        $this->setPaginationHeaders($dataProvider);

        return $dataProvider->getModels();
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
