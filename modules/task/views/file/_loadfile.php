<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

if( isset($additionalData) && ($additionalData != $model->file_group) ) {
    return;
}
/* @var $this yii\web\View */
/* @var $model app\modules\task\models\File */
/* @var $form yii\widgets\ActiveForm */

/*
<div class="file-data" id="filedata">
    <div class="form-group">
        <label class="control-label col-sm-3">Файлы</label>
        <div class="col-sm-9">
            <?= Html::a('Добавить файл', '', ['id' => 'addfilelink', 'class'=>'btn btn-default']) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
*/

if( !isset($index) ) {
    $index = '';
}
?>

    <div class="col-sm-5">
        <?php
            if( $model->file_id > 0 ) {
        ?>
            <?= Html::a($model->file_orig_name, $model->url, ['class'=>'btn btn-default btn-block', 'target'=>'_blank']) ?>
        <?php
            }
            else {
        ?>
            <?= $form->field($model, '[' . $index . ']filedata', ['template' => "{input}\n{hint}\n{error}"])->fileInput() ?>
        <?php
        }
        ?>
    </div>
    <div class="col-sm-5">
        <?= $form->field($model, '[' . $index . ']file_id', ['template'=>"{input}", 'options' => ['class' => ''], ])->hiddenInput() ?>
        <?= $form->field($model, '[' . $index . ']file_group', ['template'=>"{input}", 'options' => ['class' => ''], ])->hiddenInput() ?>
        <?= $form->field($model, '[' . $index . ']file_comment', ['template'=>"{input}\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-2">
        <?= $model->isNewRecord
            ? Html::a(
                Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']),
                '',
                [
                    'class' => 'btn btn-danger remove-file',
                ]
            )
            : '&nbsp;'
        ?>
    </div>
    <div class="clearfix"></div>

