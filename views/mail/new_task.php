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

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p><?= Html::encode('В Системе мониторинга текущих задач структурных подразделений ГАУ «ТемоЦентр» создана новая задача') ?>.</p>

<p><?= Html::encode('Отдел: ' . $department->dep_name) ?>.</p>

<p><?= Html::encode('Задача: ' . $task->task_name) . ' ' . Html::a($task->url(true), $task->url(true)) ?>.</p>

<?php if( !empty($task->task_worker_id) ) { ?>
    <p><?= Html::encode('Ответственный: ' . $task->worker->getFullName()) ?>.</p>
<?php } ?>

<?php if( !empty($task->task_direct) ) { ?>
    <p><?= Html::encode('Направление: ' . $task->task_direct) ?>.</p>
<?php } ?>

<p><?= Html::encode('Базовый срок: ' . date('d.m.Y', strtotime($task->task_finaltime))) ?>.</p>

<p><?= Html::encode('Список задач доступен по адресу: ') . Html::a(Url::to('/', true), Url::to('/', true)) ?>.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>

