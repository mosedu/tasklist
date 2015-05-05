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

$aLink = ['default/login'];

// <p>С уважением, Департамент образования города Москвы</p>

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p><?= Html::encode('Для входа в Систему мониторинга текущих задач структурных подразделений ГАУ «ТемоЦентр» используйте ссылку для входа') ?>.</p>

<p><?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>


<p>Сообщение сгенерировано автоматически, отвечать на него не нужно.</p>

