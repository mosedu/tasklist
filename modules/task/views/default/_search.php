<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\TasklistSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasklist-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'task_id') ?>

    <?= $form->field($model, 'task_dep_id') ?>

    <?= $form->field($model, 'task_num') ?>

    <?= $form->field($model, 'task_direct') ?>

    <?= $form->field($model, 'task_name') ?>

    <?php // echo $form->field($model, 'task_type') ?>

    <?php // echo $form->field($model, 'task_createtime') ?>

    <?php // echo $form->field($model, 'task_finaltime') ?>

    <?php // echo $form->field($model, 'task_actualtime') ?>

    <?php // echo $form->field($model, 'task_numchanges') ?>

    <?php // echo $form->field($model, 'task_reasonchanges') ?>

    <?php // echo $form->field($model, 'task_progress') ?>

    <?php // echo $form->field($model, 'task_summary') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
