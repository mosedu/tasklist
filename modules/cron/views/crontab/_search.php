<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cron\models\CrontabSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crontab-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'cron_id') ?>

    <?= $form->field($model, 'cron_min') ?>

    <?= $form->field($model, 'cron_hour') ?>

    <?= $form->field($model, 'cron_day') ?>

    <?= $form->field($model, 'cron_wday') ?>

    <?php // echo $form->field($model, 'cron_path') ?>

    <?php // echo $form->field($model, 'cron_tstart') ?>

    <?php // echo $form->field($model, 'cron_tlast') ?>

    <?php // echo $form->field($model, 'cron_comment') ?>

    <?php // echo $form->field($model, 'cron_isactive') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
