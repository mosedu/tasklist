<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Action */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="action-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'act_us_id')->textInput() ?>

    <?= $form->field($model, 'act_type')->textInput() ?>

    <?= $form->field($model, 'act_createtime')->textInput() ?>

    <?= $form->field($model, 'act_data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'act_table')->textInput(['maxlength' => 255]) ?>

    <?= $form->field($model, 'act_table_pk')->textInput(['maxlength' => 20]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
