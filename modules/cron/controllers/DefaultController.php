<?php

namespace app\modules\cron\controllers;

// use yii\web\Controller;
use app\modules\cron\controllers\CrontabController;

class DefaultController extends CrontabController
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}
