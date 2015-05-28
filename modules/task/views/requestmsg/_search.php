<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\RequestmsgSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="requestmsg-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'req_id') ?>

    <?= $form->field($model, 'req_user_id') ?>

    <?= $form->field($model, 'req_task_id') ?>

    <?= $form->field($model, 'req_text') ?>

    <?= $form->field($model, 'req_comment') ?>

    <?php // echo $form->field($model, 'req_created') ?>

    <?php // echo $form->field($model, 'req_data') ?>

    <?php // echo $form->field($model, 'req_is_active') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
