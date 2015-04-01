<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'us_id') ?>

    <?= $form->field($model, 'us_active') ?>

    <?= $form->field($model, 'us_email') ?>

    <?= $form->field($model, 'us_password_hash') ?>

    <?= $form->field($model, 'us_name') ?>

    <?php // echo $form->field($model, 'us_secondname') ?>

    <?php // echo $form->field($model, 'us_lastname') ?>

    <?php // echo $form->field($model, 'us_login') ?>

    <?php // echo $form->field($model, 'us_logintime') ?>

    <?php // echo $form->field($model, 'us_createtime') ?>

    <?php // echo $form->field($model, 'us_workposition') ?>

    <?php // echo $form->field($model, 'us_auth_key') ?>

    <?php // echo $form->field($model, 'us_email_confirm_token') ?>

    <?php // echo $form->field($model, 'us_password_reset_token') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
