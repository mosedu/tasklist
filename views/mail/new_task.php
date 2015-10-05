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

use app\modules\user\models\User;
use app\modules\task\models\Tasklist;
use app\modules\user\models\Department;


/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $task app\modules\task\models\Tasklist */
/* @var $department app\modules\user\models\Department */

$aLink = ['default/login'];

$task = $data['task'];
$department = $data['department'];
$worker = User::findOne($oTask->task_worker_id);

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p><?= Html::encode('В Системе мониторинга текущих задач структурных подразделений ГАУ «ТемоЦентр» создана новая задача') ?>.</p>

<table style="width: 100%">
    <tr><td style="width: 170px; vertical-align: top;">Отдел</td><td><?= Html::encode($department->dep_name) ?></td></tr>
    <?php if( !empty($task->task_direct) ) { ?>
        <tr><td style="width: 170px; vertical-align: top;">Направление</td><td><?= Html::encode($task->task_direct) ?></td></tr>
    <?php } ?>
    <tr><td style="width: 170px; vertical-align: top;">Задача</td><td><?= Html::encode($task->getTasknum() . ' ' . $task->task_name) . '<br />' . Html::a($task->url(true), $task->url(true)) ?></td></tr>
    <?php if( !empty($worker) ) { ?>
        <tr><td style="width: 170px; vertical-align: top;">Ответственный</td><td><?= Html::encode($worker->getFullName()) ?></td></tr>
    <?php } ?>
    <tr><td style="width: 170px; vertical-align: top;">Базовый срок</td><td><?= Html::encode(date('d.m.Y', strtotime($task->task_finaltime))) ?></td></tr>
</table>

<!-- p><?= '' // Html::encode($task->getTasknum() . ' ' . $task->task_name) . ' ' . Html::a($task->url(true), $task->url(true)) ?>.</p -->

<?php if( false && !empty($task->task_worker_id) ) { ?>
    <p><?= Html::encode('Ответственный: ' . $task->worker->getFullName()) ?>.</p>
<?php } ?>

<?php if( false && !empty($task->task_direct) ) { ?>
    <p><?= Html::encode('Направление: ' . $task->task_direct) ?>.</p>
<?php } ?>

<!-- p><?= '' // Html::encode('Базовый срок: ' . date('d.m.Y', strtotime($task->task_finaltime))) ?>.</p -->

<p><?= Html::encode('Список задач доступен по адресу: ') . Html::a(Url::to('/', true), Url::to('/', true)) ?>.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>

