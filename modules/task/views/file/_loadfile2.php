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

if( !isset($index) ) {
    $index = '';
}
?>

        <?php
            if( $model->file_id > 0 ) {
        ?>
            <?= Html::a(
                    '<span class="glyphicon glyphicon-floppy-disk"></span> ' . $model->file_orig_name, //  . ' [ ' . $model->humanSize() . ' ]'
                    $model->url,
                    ['class'=>'', 'target'=>'_blank', 'title'=>$model->file_comment . ' ( '.$model->humanSize().' )' . ' ' . date('d.m.Y', strtotime($model->file_time))]) // btn btn-default btn-block
            ?>
        <?php
            }
            else {
        ?>
            <div style="margin-top: 12px; background-color: #eeeeee; padding: 6px; border: 1px solid #999999; border-radius: 4px;">
            <div class="col-sm-9">
                <?= $form->field($model, '[' . $index . ']filedata', ['template' => "{input}\n{hint}\n{error}"])->fileInput() ?>
            </div>
            <div class="col-sm-3" style="text-align: right;">
                <?= Html::a(
                        Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']),
                        '',
                        [
//                            'class' => 'btn btn-danger remove-file',
                            'class' => 'text-danger remove-file',
                            'title' => 'Удалить файл',
                        ]
                    )
                ?>
            </div>

        <?php
        }
        ?>
    <div class="col-sm-12" style="<?= ($model->file_id > 0) ? 'display: none;' : '' ?>">
        <?= $form->field($model, '[' . $index . ']file_id', ['template'=>"{input}", 'options' => ['class' => ''], ])->hiddenInput() ?>
        <?= '' // $form->field($model, '[' . $index . ']file_group', ['template'=>"{input}", 'options' => ['class' => ''], ])->hiddenInput() ?>
        <?= $form->field($model, '[' . $index . ']file_comment', ['template'=>"{input}\n{hint}\n{error}"])->textInput(['maxlength' => true, 'placeholder' => $model->getAttributeLabel('file_comment')]) ?>
        <?= $form->field($model, '[' . $index . ']file_group', ['template'=>"{input}", ])->dropDownList($model->getGrous()) ?>
    </div>
    <?= ( $model->file_id == 0 ) ? '<div class="clearfix"></div></div>' : '' ?>
    <div class="clearfix"></div>

