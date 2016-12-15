<?php

namespace app\modules\user\controllers;

use app\controllers\RestController;
use app\modules\user\forms\RecoveryRequestForm;
use app\modules\user\forms\RecoveryForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ServerErrorHttpException;

/**
 * Password recovery controller.
 * 
 * @author Dmitry Erofeev <dmeroff@gmail.com>
 */
class RecoveryController extends RestController
{
    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'accessControl' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ]
                ],
            ],
            'verbFilter' => [
                'class'   => VerbFilter::class,
                'actions' => [
                    'request' => ['post'],
                    'options' => ['options'],
                    'recover' => ['put'],
                ],
            ],
        ];
    }

    /**
     * @api {post} /recovery Request recovery
     * @apiVersion 1.0.0
     * @apiGroup Recovery
     * @apiName  RequestRecovery
     * @apiDescription Creates new password recovery token and sends it by email
     * @apiParam {String} email User's email
     * @apiPermission Guest
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 204 No Content
     * @apiErrorExample {json} Validation Error:
     *     HTTP/1.1 422 Unprocessable Entity
     *     {
     *         "errors": {
     *             "email": ["First error"]
     *         }
     *     }
     */
    public function actionRequest()
    {
        $model = new RecoveryRequestForm();

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->sendRecoveryMessage()) {
            \Yii::$app->response->setStatusCode(204);
            return null;
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to send recovery code');
        }
    }

    /**
     * @api {put} /user/password Change password
     * @apiVersion 1.0.0
     * @apiGroup Recovery
     * @apiName  PasswordRecover
     * @apiDescription Updates user's password
     * @apiParam {String} password New password
     * @apiParam {String} code     Recovery code
     * @apiPermission Guest
     * @apiSuccessExample Success-Response:
     *     HTTP/1.1 204 No Content
     * @apiErrorExample {json} Validation Error:
     *     HTTP/1.1 422 Unprocessable Entity
     *     {
     *         "errors": {
     *             "password": ["First error"],
     *             "code": ["First error"],
     *         }
     *     }
     */
    public function actionRecover()
    {
        $model = new RecoveryForm();

        $model->load(\Yii::$app->getRequest()->getBodyParams(), '');

        if ($model->recoverPassword()) {
            \Yii::$app->response->setStatusCode(204);
            return null;
        } elseif ($model->hasErrors()) {
            \Yii::$app->response->setStatusCode(422);
            return ['errors' => $model->getErrors()];
        } else {
            throw new ServerErrorHttpException('Failed to recover password');
        }
    }
    
    
        public function actionOptions()
    {
        \Yii::$app->response->setStatusCode(200);
    }
}