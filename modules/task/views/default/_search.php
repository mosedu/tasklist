<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;

use app\modules\user\models\Department;
use app\modules\task\models\Tasklist;


if( !isset($action) ) {
    $action = ['index'];
}
$sCss = <<<EOT
.col-sm-1-dop {
/*    width: 8.33333%;*/
    width: 12.4999%;
}
.col-sm-11-dop {
/*    width: 91.6667%;*/
    width: 87.5%;
}
EOT;

// $this->registerCss($sCss);

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\TasklistSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasklist-search" id="<?= $idserchblock; ?>" style="<?= 'display: none; ' ?>clear: both; border: 1px solid #777777; border-radius: 4px; background-color: #eeeeee; padding-top: 2em; padding-bottom: 1em; margin-bottom: 2em;">

    <?php $form = ActiveForm::begin([
        'action' => $action,
        'method' => 'get',
        'id' => 'filter-message-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
//                'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
            'horizontalCssClasses' => [
                'label' => 'col-sm-3',
                'offset' => 'col-sm-offset-3',
                'wrapper' => 'col-sm-9',
//                    'error' => '',
//                    'hint' => '',
            ],
        ],
    ]);

    $aSubjectParam = [
        'horizontalCssClasses' => [
            'label' => 'col-sm-2 col-sm-1-dop',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-10 col-sm-11-dop',
        ],
        'inputOptions' => [
//            'disabled' => true,
        ]
    ];

    $aNumParam = [
        'horizontalCssClasses' => [
            'label' => 'col-sm-6',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-6',
        ],
        'inputOptions' => [
//            'disabled' => true,
        ]
    ];
    ?>

    <?php //echo $form->field($model, 'task_id') ?>

    <div class="col-sm-2">
        <?= $form->field($model, 'task_num', $aNumParam) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'task_dep_id')->dropDownList(Department::getList(false)) ?>
    </div>

    <div class="col-sm-6">
        <?= $form->field($model, 'task_direct') ?>
    </div>

    <div class="col-sm-12">
        <?= $form->field($model, 'task_name', $aSubjectParam) ?>
    </div>

    <?php // echo $form->field($model, 'task_type') ?>

    <?php // echo $form->field($model, 'task_createtime') ?>

    <?php // echo $form->field($model, 'task_finaltime') ?>

    <?php // echo $form->field($model, 'task_actualtime') ?>

    <?php // echo $form->field($model, 'task_numchanges') ?>

    <?php // echo $form->field($model, 'task_reasonchanges') ?>

    <?php // echo $form->field($model, 'task_progress') ?>

    <?php // echo $form->field($model, 'task_summary') ?>

    <div class="col-sm-12">
        <!-- div class="form-group" -->
        <?= Html::a('Сбросить настройки', $action, ['class' => 'btn btn-default pull-right']) ?>
        <div class="pull-right" style="width: 2em;">&nbsp;</div>
        <?= Html::submitButton('Искать', ['class' => 'btn btn-success pull-right']) ?>
        <!-- /div -->
    </div>
    <div class="clearfix"></div>

    <!-- div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div -->

    <?php ActiveForm::end(); ?>

</div>
