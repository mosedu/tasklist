<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\ActionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="action-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'act_id') ?>

    <?= $form->field($model, 'act_us_id') ?>

    <?= $form->field($model, 'act_type') ?>

    <?= $form->field($model, 'act_createtime') ?>

    <?= $form->field($model, 'act_data') ?>

    <?php // echo $form->field($model, 'act_table') ?>

    <?php // echo $form->field($model, 'act_table_pk') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
