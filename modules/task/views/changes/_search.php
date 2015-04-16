<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\ChangesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="changes-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'ch_id') ?>

    <?= $form->field($model, 'ch_us_id') ?>

    <?= $form->field($model, 'ch_task_id') ?>

    <?= $form->field($model, 'ch_data') ?>

    <?= $form->field($model, 'ch_text') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
