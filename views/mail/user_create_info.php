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

$aLink = ['login'];

// <p>С уважением, Департамент образования города Москвы</p>

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>!</p>

<p>Вы зарегистрированы на сайте <?= Html::encode(Yii::$app->name) ?>.</p>

<p>Для входа перейдите по ссылке: <?= Html::a(Url::to($aLink, true), Url::to($aLink, true)) ?></p>

<p>Данные для входа:</p>

<p>Email: <?= Html::encode($model->us_email) ?></p>

<p>Пароль: <?= Html::encode($model->newPassword) ?></p>



<p>Сообщение сгенерировано автоматически, отвечать на него не нужно</p>

