<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\File */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="file-form">

    <?= $form->field($model, 'file_time')->textInput() ?>
    <?= $form->field($model, 'file_comment')->textInput(['maxlength' => true]) ?>

</div>
