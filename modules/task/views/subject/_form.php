<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Subject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'subj_title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'subj_created')->textInput() ?>

    <?= $form->field($model, 'subj_dep_id')->textInput() ?>

    <?= $form->field($model, 'subj_comment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'subj_is_active')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
