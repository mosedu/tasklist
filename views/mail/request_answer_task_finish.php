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

$aData = unserialize($data['request']->req_data);
?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p><?= Html::encode(
        'Ваш запрос на перенос даты окончания задачи '
      . $data['request']->task->task_name
      . ' на '
      . date("d.m.Y", strtotime($aData['task_finishtime']))
      . ' '
      . ($data['isok'] ? 'одобрен' : 'отклонен')
      . '.'
    ) ?></p>

