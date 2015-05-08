<?php

namespace app\modules\main\controllers;

use yii;
use yii\web\Controller;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\main\models\MessageForm;

class DefaultController extends Controller
{
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionSupport()
    {
        $model = new MessageForm();

        if( Yii::$app->request->isAjax ) {
            if( $model->load(Yii::$app->request->post()) ) {
                Yii::$app->response->format = Response::FORMAT_JSON;
                $aRet = ActiveForm::validate($model);
                Yii::info('actionMessage(): return json ' . print_r($aRet, true));
                if( count($aRet) == 0 ) {
                    if( $model->contact() ) {
                        $aRet = ['result' => true];
                    }
                    else {
                        $aRet[Html::getInputId($model, 'body')] = ['Ошибка отправки сообщения'];
                    }
                }
                return $aRet;
            }
            else {
                return $this->renderAjax(
                    'message',
                    [
                        'model' => $model,
                    ]
                );
            }
        }

        if ($model->load(Yii::$app->request->post()) /*&& $model->save() */) {
            return $this->redirect('/');
        } else {
            return $this->render(
                'message',
                [
                    'model' => $model,
                ]
            );
        }
    }
}
