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
use app\modules\task\models\Tasklist;
use app\modules\user\models\User;
use app\modules\user\models\Department;
// use app\modules\task\models\Worker;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/** @var app\modules\task\models\Tasklist $oTask */

$oTask = $data['task'];
$department = Department::findOne($oTask->task_dep_id);
$worker = User::findOne($oTask->task_worker_id);
?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p><?= Html::encode('В Системе мониторинга текущих задач структурных подразделений ГАУ «ТемоЦентр» Вам назначена задача') ?>.</p>

<table style="width: 100%">
    <tr><td style="width: 170px; vertical-align: top;">Отдел</td><td><?= Html::encode($department->dep_name) ?></td></tr>
    <?php if( !empty($oTask->task_direct) ) { ?>
        <tr><td style="width: 170px; vertical-align: top;">Направление</td><td><?= Html::encode($oTask->task_direct) ?></td></tr>
    <?php } ?>
    <tr><td style="width: 170px; vertical-align: top;">Задача</td><td><?= Html::encode($oTask->getTasknum() . ' ' . $oTask->task_name) . '<br />' . Html::a($oTask->url(true), $oTask->url(true)) ?></td></tr>
    <?php if( !empty($worker) ) { ?>
        <tr><td style="width: 170px; vertical-align: top;">Ответственный</td><td><?= Html::encode($worker->getFullName()) ?></td></tr>
    <?php } ?>
    <tr><td style="width: 170px; vertical-align: top;">Базовый срок</td><td><?= Html::encode(date('d.m.Y', strtotime($oTask->task_finaltime))) ?></td></tr>
</table>

<!-- p><?= '' // $oTask->getTasknum() . ' ' . Html::encode($oTask->task_name) . ' ' . Html::a($oTask->url(true), $oTask->url(true)) ?></p -->

<!-- p>Дата исполнения: <?= '' // date('d.m.Y', strtotime($oTask->task_actualtime)) ?></p -->

<p><?= Html::encode('Список задач доступен по адресу: ') . Html::a(Url::to('/', true), Url::to('/', true)) ?>.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>

