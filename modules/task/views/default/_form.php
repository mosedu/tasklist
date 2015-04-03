<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;

use app\modules\user\models\Department;
use app\modules\task\models\Tasklist;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */
/* @var $form yii\widgets\ActiveForm */

$aTextParam = [
    'horizontalCssClasses' => [
        'label' => 'col-sm-2',
        'offset' => 'col-sm-offset-2',
        'wrapper' => 'col-sm-10',
    ],
];

$aTextParamSummary = [
    'horizontalCssClasses' => [
        'label' => 'col-sm-4',
        'offset' => 'col-sm-offset-4',
        'wrapper' => 'col-sm-8',
    ],
];

$aDisable = [];
if( !Yii::$app->user->can('createUser') ) {
    $aDisable = ['readonly' => true, 'disabled' => true];
}
?>

<div class="tasklist-form">
    <?php
/*
    <?= $form->field($model, 'task_num')->textInput() ?>
    <?= $form->field($model, 'task_createtime')->textInput() ?>
    <?= $form->field($model, 'task_finaltime')->textInput() ?>
    <?= $form->field($model, 'task_numchanges')->textInput() ?>
    <?= $form->field($model, 'task_reasonchanges')->textarea(['rows' => 6]) ?>
    <?= $form->field($model, 'task_progress')->textInput() ?>

*/
    ?>

    <?php $form = ActiveForm::begin([
        'id' => 'message-form',
        'layout' => 'horizontal',
        'options'=>[
//            'enctype'=>'multipart/form-data'
        ],
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-sm-3',
                'offset' => 'col-sm-offset-3',
                'wrapper' => 'col-sm-9',
                'hint' => 'col-sm-9 col-sm-offset-3',
            ],
        ],
    ]); ?>

    <div class="col-sm-8">
        <?= $form->field($model, 'task_direct', $aTextParam)->textarea(['rows' => 2]) ?>
        <?= $form->field($model, 'task_name', $aTextParam)->textarea(['rows' => 2]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'task_dep_id')->dropDownList(Department::getList(false), $aDisable) ?>
        <?= $form->field($model, 'task_type')->dropDownList(Tasklist::getAllTypes()) ?>
        <?= $form->field($model, 'task_actualtime')->widget(
            DatePicker::className(),
            [
                'model' => $model,
                'attribute' => 'task_actualtime',
                'type' => DatePicker::TYPE_COMPONENT_APPEND,
                //            'pickerButton' => false,
                'removeButton' => false,
                'options' => [
                    'placeholder' => 'Срок исполнения',
                ],
                'pluginOptions' => [
                    'autoclose'=>true,
                ]
            ]
        ) ?>
        <?= $form->field($model, 'task_progress')->dropDownList(Tasklist::getAllProgresses()) ?>
    </div>

    <?php
        if( !$model->isNewRecord ) {
    ?>
        <div class="col-sm-8">
            <?= $form->field($model, 'task_summary', $aTextParam)->textarea(['rows' => 4]) ?>
        </div>
    <?php
        }
    ?>

    <div class="clearfix"></div>

    <div class="col-sm-8">
        <div class="col-sm-2">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
