<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasklist-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'task_dep_id')->textInput() ?>

    <?= $form->field($model, 'task_num')->textInput() ?>

    <?= $form->field($model, 'task_direct')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'task_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'task_type')->textInput() ?>

    <?= $form->field($model, 'task_createtime')->textInput() ?>

    <?= $form->field($model, 'task_finaltime')->textInput() ?>

    <?= $form->field($model, 'task_actualtime')->textInput() ?>

    <?= $form->field($model, 'task_timechanges')->textInput() ?>

    <?= $form->field($model, 'task_reasonchanges')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'task_progress')->textInput() ?>

    <?= $form->field($model, 'task_summary')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
