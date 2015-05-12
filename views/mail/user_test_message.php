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

?>

<p>Здравствуйте, <?= Html::encode($model->getShortName()) ?>.</p>

<p>Это тестовое сообщение.</p>

<p>Нужно для проверки на спам.</p>

