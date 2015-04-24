<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\helpers\Json;
use kartik\date\DatePicker;
use kartik\select2\Select2;

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

if( $model->actdatestart && preg_match('|^(\\d{4})-(\\d{2})-(\\d{2})$|', $model->actdatestart, $a) ) {
    $model->actdatestart = "{$a[3]}.{$a[2]}.{$a[1]}";
}
if( $model->actdatefinish && preg_match('|^(\\d{4})-(\\d{2})-(\\d{2})$|', $model->actdatefinish, $a) ) {
    $model->actdatefinish = "{$a[3]}.{$a[2]}.{$a[1]}";
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

    $aProgress = [
        'horizontalCssClasses' => [
            'label' => 'col-sm-2 col-sm-1-dop',
            'offset' => 'col-sm-offset-1',
            'wrapper' => 'col-sm-10 col-sm-11-dop',
        ],
        'inputOptions' => [
//            'disabled' => true,
        ]
    ];

    $aProgressWidget = [
        'data' => Tasklist::getAllProgresses(),
        'language' => 'ru',
        'options' => [
            'multiple' => true,
//           'tags' => true,
            'placeholder' => 'Выберите из списка ...',
        ],
        'pluginOptions' => [
            'allowClear' => true,
        ],
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

    $sIdProgressList = Html::getInputId($model, 'task_progress');
    $sStartDate = Html::getInputId($model, 'datestart');
    $sFinishDate = Html::getInputId($model, 'datefinish');
    $sStartActDate = Html::getInputId($model, 'actdatestart');
    $sFinishActDate = Html::getInputId($model, 'actdatefinish');

    $aAttr = $model->safeAttributes();
    $aDel = ['showFilterForm', 'showFinishedTask', 'showTaskSummary'];
    $a = array_flip($aAttr);
//    echo print_r($a, true);
    foreach($aDel As $sd) {
        if( isset($a[$sd]) ) {
            unset($a[$sd]);
        }
    }
//    echo print_r($a, true);
    $aAttr = array_keys($a);
//    echo print_r($aAttr, true);

    $aTestId = [];
    foreach($aAttr As $v) {
        $aTestId[] = Html::getInputId($model, $v);
    }
    $sTestId = Json::encode($aTestId);

    $sClearText = Json::encode([
        Html::getInputId($model, 'task_num'),
        Html::getInputId($model, 'task_direct'),
        Html::getInputId($model, 'task_name'),
        Html::getInputId($model, 'actdatestart'),
        Html::getInputId($model, 'actdatefinish'),
    ]);
    $sUnset = Json::encode([
        Html::getInputId($model, 'task_type'),
    ]);
    $sVal = Json::encode([Tasklist::PROGRESS_STOP, Tasklist::PROGRESS_WAIT, Tasklist::PROGRESS_WORK]);
    //    console.log(jQuery("#{$sIdProgressList} :selected"));
    $sJs = <<<EOT
    var aClear = {$sClearText},
        aUnset = {$sUnset},
        formatDate = function(date) {
            var d = "" + date.getDate(),
                m = "" + (date.getMonth() + 1),
                y = date.getFullYear();
            return ((d.length < 2) ? ("0" + d) : d)
                 + "."
                 + ((m.length < 2) ? ("0" + m) : m)
                 + "."
                 + y;
        },
        clearFields = function() {
            for(var i in aClear) {
                jQuery("#" + aClear[i]).val("");
            }
            for(var i in aUnset) {
                jQuery("#" + aUnset[i] + " option").prop("selected", false);
            }
        },
        strtodate = function(s) {
            var a;
            s = s.replace(/\s/g, "");
            if( s == "" ) {
                return null;
            }
            a = /^(\d{1,2})\.(\d{1,2})\.(\d{4})$/.exec(s);
            return new Date(parseInt(a[3], 10), parseInt(a[2], 10)-1, parseInt(a[1], 10));
        },
        setupDateInterval = function(idStart, idFinish){
            var obStart = jQuery(idStart),
                obFinish = jQuery(idFinish),
                sStart = obStart.val(),
                sFinish = obFinish.val(),
                dStart = strtodate(sStart),
                dFinish = strtodate(sFinish);

            if( (dStart === null) && (dFinish === null) ) {
                obStart.datepicker("setEndDate", dFinish);
                obFinish.datepicker("setStartDate", dStart);
                return;
            }

            if( (dStart !== null) && (dFinish !== null) ) {
                if( dFinish < dStart ) {
                    var t = dStart;
                    dStart = dFinish;
                    dFinish = t;
                    obStart.val(sFinish);
                    obFinish.val(sStart);
                }
            }

            obFinish.datepicker("setStartDate", dStart);
            obStart.datepicker("setEndDate", dFinish);
        };

jQuery("#setfilter7day").on("click", function(event){
    var dCur = new Date();
    event.preventDefault();
    clearFields();
    jQuery("#{$sIdProgressList}").select2("val", $sVal);

    jQuery("#{$sStartDate}").val(formatDate(dCur));
    dCur.setDate(dCur.getDate() + 8);
    jQuery("#{$sFinishDate}").val(formatDate(dCur));
    setupDateInterval("#{$sStartDate}", "#{$sFinishDate}");
    return false;
});

jQuery("#setfilterover").on("click", function(event){
    var dCur = new Date();
    event.preventDefault();
    clearFields();
    jQuery("#{$sIdProgressList}").select2("val", $sVal);

    jQuery("#{$sStartDate}").val("");
    jQuery("#{$sFinishDate}").val(formatDate(dCur));
    setupDateInterval("#{$sStartDate}", "#{$sFinishDate}");
    return false;
});

setupDateInterval("#{$sStartDate}", "#{$sFinishDate}");
setupDateInterval("#{$sStartActDate}", "#{$sFinishActDate}");
EOT;
    ?>

    <?php //echo $form->field($model, 'task_id') ?>

    <div class="col-sm-4">
        <?= $form->field($model, 'task_num', $aNumParam) ?>
    </div>

    <div class="col-sm-4">
        <?php echo $form->field($model, 'task_type')->dropDownList(array_merge(['' => ''], Tasklist::getAllTypes())) ?>
    </div>

    <?php
    if( Yii::$app->user->can('createUser') ) {
        ?>

        <div class="col-sm-4">
            <?= $form->field($model, 'task_dep_id')->dropDownList(array_merge(['' => ''], Department::getList(false))) ?>
        </div>

    <?php
    }
    ?>

    <div class="col-sm-12">
        <?php echo $form->field($model, 'task_progress', $aProgress)
            ->widget(Select2::classname(), $aProgressWidget)
        ?>
        <?php // echo $form->field($model, 'task_progress')->dropDownList(array_merge(['' => ''], Tasklist::getAllProgresses())) ?>
    </div>

    <div class="col-sm-12">
        <?= $form->field($model, 'task_direct', $aSubjectParam) ?>
    </div>

    <div class="col-sm-12">
        <?= $form->field($model, 'task_name', $aSubjectParam) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'datestart', $aNumParam)->widget(
            DatePicker::className(),
            [
            'options' => ['placeholder' => 'от (≥)'],
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
                'options' => ['placeholder' => 'до (<)'],
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
    </div>

    <div class="col-sm-2">
        <div class="btn-group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                Фильтры<span class="caret"></span>
            </button>
            <ul class="dropdown-menu" role="menu">
                <li><a href="#" class="" id="setfilterover">Просроченные</a></li>
                <li><a href="#" class="" id="setfilter7day">Приближающиеся</a></li>
                <!-- li class="divider"></li -->
            </ul>
        </div>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form->field($model, 'actdatestart', $aNumParam)->widget(
            DatePicker::className(),
            [
                'options' => ['placeholder' => 'от (≥)'],
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                    'autoclose'=>true,
                ],
                'pluginEvents' => [
                    'changeDate' => 'function(event) {
                    var startDate = new Date(event.date.valueOf());
                    jQuery("#tasklistsearch-actdatefinish").datepicker("setStartDate", startDate);
                }',
                ],
            ]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'actdatefinish', $aDateParam)->widget(
            DatePicker::className(),
            [
                'options' => ['placeholder' => 'до (<)'],
                'type' => DatePicker::TYPE_INPUT,
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'todayHighlight' => true,
                    'autoclose'=>true,
                ],
                'pluginEvents' => [
                    'changeDate' => 'function(event) {
                        var startDate = new Date(event.date.valueOf());
                        jQuery("#tasklistsearch-actdatestart").datepicker("setEndDate", startDate);
                    }',
                ],
            ]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form->field(
            $model,
            'numchangesstart',
            $aNumParam
        )
        ->input('text', ['placeholder' => 'от'])?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'numchangesfinish', $aDateParam)->input('text', ['placeholder' => 'до']) ?>
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
            $sIdFilterCb = Html::getInputId($model, 'showFilterForm');
            Yii::$app->params['panelcheckbox'] = array_merge(
                Yii::$app->params['panelcheckbox'],
                [
                    $sIdFilterCb => [
                        'icon' => 'search',
                        'name' => Html::getInputName($model, 'showFilterForm'),
                        'title' => Html::encode('Показать/скрыть панель фильтрации'),
                        'callback' => "
                            // oButton - function parametr
                            var oPanel = jQuery(\"#{$idserchblock}\"),
                                isFormEmpty = function() {
                                    var aTestId = {$sTestId},
                                        isEmpty = true;
                                    for(var i in aTestId) {
                                        var ob = jQuery(\"#\" + aTestId[i]);
                                        if( ob && (ob.val() !== undefined) && (ob.val() != '') && (ob.val() !== null) ) {
                                            isEmpty = false;
                                            break;
                                        }
                                    }
                                    return isEmpty;
                                },
                                showSearchIndicator = function(bShow) {
                                    var ob = jQuery(\"#id_empty_search\");
                                    if( !bShow ) {
                                        ob.hide();
                                        return;
                                    }
                                    if( isFormEmpty() ) {
                                        // hide indicator
                                        ob.hide();
                                    }
                                    else {
                                        // show indicator
                                        ob.show();
                                    }
                                };

                            if( oPanel.is(\":hidden\") ) {
                                oPanel.show();
                                showSearchIndicator(false);
                                oButton.addClass(\"panelcb-on\");
                                oButton.removeClass(\"panelcb-of\");
                            }
                            else {
                                oPanel.hide();
                                showSearchIndicator(true);
                                oButton.removeClass(\"panelcb-on\");
                                oButton.addClass(\"panelcb-of\");
                            }
                            jQuery.ajax({
                                data: " . '{' . $model->formName().": {showFilterForm: jQuery(\"#{$sIdFilterCb}\").is(\":checked\") ? 1: 0 }}
                            });
                        ",
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

    <?php

        ActiveForm::end();
        $this->registerJs($sJs);

    ?>




</div>
