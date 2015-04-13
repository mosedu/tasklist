<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use kartik\date\DatePicker;

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

if( $model->datestart && preg_match('|^(\\d{4})-(\\d{2})-(\\d{2})$|', $model->datestart, $a) ) {
    $model->datestart = "{$a[3]}.{$a[2]}.{$a[1]}";
}
if( $model->datefinish && preg_match('|^(\\d{4})-(\\d{2})-(\\d{2})$|', $model->datefinish, $a) ) {
    $model->datefinish = "{$a[3]}.{$a[2]}.{$a[1]}";
}
// $this->registerCss($sCss);

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\TasklistSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tasklist-search" id="<?= $idserchblock; ?>" style="<?= ($model->showFilterForm ? '' : 'display: none; ') ?>clear: both; border: 1px solid #777777; border-radius: 4px; background-color: #eeeeee; padding-top: 2em; padding-bottom: 1em; margin-bottom: 2em;">

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

    $aDateParam = [
        'horizontalCssClasses' => [
            'label' => 'col-sm-3',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-6',
        ],
        'inputOptions' => [
//            'disabled' => true,
        ]
    ];

    $aCheckBoxOptions = [
//        'template' => "<div class=\"col-sm-4\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        'template' => "<div class=\"checkbox col-sm-4\">\n{beginLabel}\n{input}\n{labelTitle}\n{endLabel}\n<div class=\"col-sm-12\">{error}</div>\n{hint}\n</div>",
    ];
    ?>

    <?php //echo $form->field($model, 'task_id') ?>

    <div class="col-sm-4">
        <?= $form->field($model, 'task_num', $aNumParam) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'task_dep_id')->dropDownList(array_merge(['0' => ''], Department::getList(false))) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'task_direct', $aNumParam) ?>
    </div>

    <div class="col-sm-12">
        <?= $form->field($model, 'task_name', $aSubjectParam) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'datestart', $aNumParam)->widget(
            DatePicker::className(),
            [
            'options' => ['placeholder' => 'от'],
            'type' => DatePicker::TYPE_INPUT,
            'pluginOptions' => [
                'format' => 'dd.mm.yyyy',
                'todayHighlight' => true,
                'autoclose'=>true,
            ],
            'pluginEvents' => [
                'changeDate' => 'function(event) {
                    var startDate = new Date(event.date.valueOf());
                    jQuery("#tasklistsearch-datefinish").datepicker("setStartDate", startDate);
                }',
        ],
        ]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'datefinish', $aDateParam)->widget(
            DatePicker::className(),
            [
                'options' => ['placeholder' => 'до'],
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                    'autoclose'=>true,
                ],
                'pluginEvents' => [
                    'changeDate' => 'function(event) {
                        var startDate = new Date(event.date.valueOf());
                        jQuery("#tasklistsearch-datestart").datepicker("setEndDate", startDate);
                    }',
                ],
            ]) ?>
    </div>

    <div class="col-sm-2">
        <?php echo $form->field($model, 'task_type')->dropDownList(array_merge(['' => ''], Tasklist::getAllTypes())) ?>
    </div>

    <div class="col-sm-2">
        <?php echo $form->field($model, 'task_progress')->dropDownList(array_merge(['' => ''], Tasklist::getAllProgresses())) ?>
    </div>

    <?php // echo $form->field($model, 'task_createtime') ?>

    <?php // echo $form->field($model, 'task_finaltime') ?>

    <?php // echo $form->field($model, 'task_actualtime') ?>

    <?php // echo $form->field($model, 'task_numchanges') ?>

    <?php // echo $form->field($model, 'task_reasonchanges') ?>

    <?php // echo $form->field($model, 'task_progress') ?>

    <?php // echo $form->field($model, 'task_summary') ?>

    <div class="col-sm-8"><div style="display: none">
        <?php
            if( !isset(Yii::$app->params['panelcheckbox']) ) {
                Yii::$app->params['panelcheckbox'] = [];
            }
            Yii::$app->params['panelcheckbox'] = array_merge(
                Yii::$app->params['panelcheckbox'],
                [
                    Html::getInputId($model, 'showFilterForm') => [
                        'icon' => 'search',
                        'name' => Html::getInputName($model, 'showFilterForm'),
                        'title' => Html::encode('Показать/скрыть панель фильтрации'),
                    ],
                    Html::getInputId($model, 'showFinishedTask') => [
                        'icon' => 'ok',
                        'name' => Html::getInputName($model, 'showFinishedTask'),
                        'title' => Html::encode('Показать/скрыть завершенные задачи'),
                    ],
                    Html::getInputId($model, 'showTaskSummary') => [
                        'icon' => 'file',
                        'name' => Html::getInputName($model, 'showTaskSummary'),
                        'title' => Html::encode('Показать/скрыть поле Отчет'),
                    ],
                ]
            );

            echo $form->field($model, 'showFilterForm')->checkbox($aCheckBoxOptions);
            echo $form->field($model, 'showFinishedTask')->checkbox($aCheckBoxOptions);
            echo $form->field($model, 'showTaskSummary')->checkbox($aCheckBoxOptions);
        ?>
        </div></div>
    <div class="col-sm-4">
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
