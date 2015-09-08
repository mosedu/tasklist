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


/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/** @var app\modules\task\models\Tasklist $oTask */

$oTask = $data['task'];

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p><?= Html::encode('В Системе мониторинга текущих задач структурных подразделений ГАУ «ТемоЦентр» у назначенной Вам задачи') ?> <br />
<?= Html::encode($oTask->task_name) ?> <br />
приближается срок исполнения.</p>

<p>Дата исполнения: <?= date('d.m.Y', strtotime($oTask->task_actualtime)) ?></p>

<p><?= 'Ссылка на задачу: ' . Html::a($oTask->url(true), $oTask->url(true)) ?></p>

<p><?= Html::encode('Список задач доступен по адресу: ') . Html::a(Url::to('/', true), Url::to('/', true)) ?>.</p>

<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>

