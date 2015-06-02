<?php
/**
 * User: KozminVA
 * Date: 24.03.2015
 * Time: 11:00
 *
 * user_notif_show
 * шаблон уведомления пользователя при показе сообщения на сайте
 *
 */

//use yii;
use yii\helpers\Html;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\User */

$taskLink = ['/task/default/view', 'id'=>$data['task']->task_id];
$reqLink = ['/task/requestmsg'];
$aData = unserialize($data['request']->req_data);

// <p>Для просмотра задачи перейдите по ссылке: <?= Html::a(Url::to($taskLink, true), Url::to($taskLink, true)) ?></p>

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p><?= Html::encode(
        $data['user']->getFullName()
      . ' просит перенести дату окончания задачи '
      . $data['task']->task_name
      . ' с '
      . date("d.m.Y", strtotime($data['task']->task_finishtime))
      . ' на '
      . date("d.m.Y", strtotime($aData['task_finishtime']))
      . '. Причина: '
      . $data['request']->req_text
      . '.'
    ) ?></p>

<p>Для просмотра запросов перейдите по ссылке: <?= Html::a(Url::to($reqLink, true), Url::to($reqLink, true)) ?></p>





