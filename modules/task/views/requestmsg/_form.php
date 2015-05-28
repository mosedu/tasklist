<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Requestmsg */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="requestmsg-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'req_user_id')->textInput() ?>

    <?= $form->field($model, 'req_task_id')->textInput() ?>

    <?= $form->field($model, 'req_text')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'req_comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'req_created')->textInput() ?>

    <?= $form->field($model, 'req_data')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'req_is_active')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
