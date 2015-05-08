<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Url;

use app\modules\user\models\Department;
use app\modules\task\models\Tasklist;
use kartik\date\DatePicker;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */
/* @var $form yii\widgets\ActiveForm */

$aTextParam = [
    'horizontalCssClasses' => [
        'label' => 'col-sm-3',
        'offset' => 'col-sm-offset-3',
        'wrapper' => 'col-sm-9',
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
$bCanChange = $model->canChangeDate();

$aDirect = []; // Tasklist::getAllStatuses();

$aSetting = [
    'data' => [], // $aDirect,
    'language' => 'ru',
    'name' => 'selectdirection',
    'options' => [
        'multiple' => false,
        'placeholder' => 'Выберите из списка ...',
    ],
    'pluginOptions' => [
        'allowClear' => true,
    ],
];

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
    ]);

    $bFinished = ($model->task_progress == Tasklist::PROGRESS_FINISH);
    $bEditDates = !$bFinished || Yii::$app->user->can('createUser');

    ?>

    <div class="col-sm-8">
        <?= $form->field($model, 'task_direct', $aTextParam)
            ->textarea(array_merge(['rows' => 2], $bEditDates ? [] : $aDisable))
            ->hint(Html::tag('div', Html::a('Выбрать направление', '', ['id'=>'idshowselectdirection',]), ['style'=>'text-align: right;'])) ?>

        <?= $form->field($model, 'task_name', $aTextParam)->textarea(array_merge(['rows' => 2], $bEditDates ? [] : $aDisable)) ?>

        <?= $form->field($model, 'task_summary', array_merge($aTextParam, ['options' => ($model->isNewRecord || (strlen($model->task_summary) == 0)) ? ['style' => 'display: none;', 'class'=>'form-group'] : ['class'=>'form-group']]) )->textarea(['rows' => 4, 'data-req' => $bFinished ? 1 : 0, ]) ?>

        <?php
        if( $model->isNewRecord ) { // new record
            $this->registerJs('var aChache = {},
            oSel = jQuery("#idselectdirectlist"),
            setDirectList = function(data) {
                oSel.find("option").remove();
                for(var i in data) {
                    oSel.append("<option value=\\""+i+"\\">"+data[i]+"</option>");
                }
            };
            jQuery("#idshowselectdirection").on("click", function(event) {
            var nDepId = jQuery("#'.Html::getInputId($model, 'task_dep_id').'").val();
            event.preventDefault();
            if( nDepId in aChache ) {
                setDirectList(aChache[nDepId]);
            }
            else {
                jQuery.ajax({url: "' . Url::to(['default/lastdirect']) . '", data: {depid: nDepId}, dataType: "json", success: function(data, textStatus, jqXHR){
                    aChache[nDepId] = data;
                    setDirectList(data);
                }});
            }
            $("#selectmodal").modal("show");
            return false; });',
            View::POS_READY,
            'showselectinstr');

            Modal::begin([
                'header' => 'Выбрать направление из списка',
                'id' => 'selectmodal',
                'footer' => '<button type="button" class="btn btn-default" data-dismiss="modal">Отмена</button><button type="button" class="btn btn-primary" id="setselecteddata">Вставить</button>',
            ]);

            // echo Select2::widget($aSetting);
            echo Html::listBox(
                'selectdir',
                '',
                $aDirect,
                ['id' => 'idselectdirectlist', 'class'=>'form-control', 'multiple' => true]
            );

            Modal::end();
            $this->registerJs('jQuery("#setselecteddata")
                .on("click", function(event) {
                    var s = jQuery("#idselectdirectlist option:selected").text();
                    // console.log("Selected data : " + s);
                    event.preventDefault();
                    if( s.length > 0 ) { jQuery("#'.Html::getInputId($model, 'task_direct').'").val(s); }
                    jQuery("#selectmodal").modal("hide");
                    return false;
                });
                jQuery("#idselectdirectlist").on("dblclick", function(event){
                    jQuery("#setselecteddata").trigger("click");
                });',
                View::POS_READY,
                'selectdirection');
        }

        $sIdProgress = Html::getInputId($model, 'task_progress');
        $sIdSummary = Html::getInputId($model, 'task_summary');
        $nFinish = Tasklist::PROGRESS_FINISH;
        $sJs = <<<EOT
var oSelProgress = jQuery("#{$sIdProgress}"),
    oSummary = jQuery("#{$sIdSummary}"),
    oDivSummary = jQuery(".field-tasklist-task_summary");
oSelProgress.on(
    "change",
    function(event) {
        if( jQuery(this).val() == {$nFinish} ) {
            oSummary.attr("data-req", 1);
            oDivSummary.show();
        }
        else {
            oSummary.attr("data-req", 0);
//            oDivSummary.hide();
        }
    }
);
EOT;
        $this->registerJs($sJs, View::POS_READY, 'changeprogress');

        ?>

        <?= $form->field(
            $model,
            'task_reasonchanges',
            array_merge($aTextParam, ['options' => ['style' => (strlen($model->task_reasonchanges) > 0 ? '' : 'display: none;'), 'class' => "form-group field-tasklist-reasonchange"]])
        )->textarea(array_merge(['rows' => 3, 'data-old'=>$model->isNewRecord ? '' : $model->_oldAttributes['task_actualtime'],], $bEditDates ? [] : $aDisable)) ?>
    </div>

    <div class="col-sm-4">
                <?= $form->field($model, 'task_dep_id')->dropDownList(Department::getList(false), $aDisable) ?>
                <?= $form->field($model, 'task_type')->dropDownList(Tasklist::getAllTypes(), $bEditDates ? [] : $aDisable) ?>
                <?= $form->field($model, 'task_progress')->dropDownList(Tasklist::getAllProgresses(), $bEditDates ? [] : $aDisable) ?>
                <?= $form->field(
                    $model,
                    'task_actualtime',
                    [
                        'labelOptions'=>[
                            'label'=>$model->getAttributeLabel($model->isNewRecord ? 'task_finaltime' : 'task_actualtime'),
                        ],
                    ]
                )->widget(
                    DatePicker::className(),
                    [
                        'model' => $model,
                        'attribute' => 'task_actualtime',
                        'disabled' => !$bEditDates,
                        'readonly' => !$bEditDates,
                        'type' => DatePicker::TYPE_COMPONENT_APPEND,
                        //            'pickerButton' => false,
                        'removeButton' => false,
                        'options' => [
                            'placeholder' => 'Срок исполнения',
                        ],
                        'pluginOptions' => [
                            'autoclose' => true,
                        ],
                        'pluginEvents' => [
                            "changeDate" => "function(e) {
//                    console.log(e);
                     var dt = new Date(e.date),
                         s = '',
                         sCompare = dt.getFullYear() + '',
                         n = dt.getDate(),
                         ob = jQuery('.field-tasklist-reasonchange');
//                     console.log('ob = ', ob);
                     s += ((n < 10) ? '0' : '') + n + '.';
                     n = dt.getMonth() + 1;
                     s += ((n < 10) ? '0' : '') + n + '.';
                     sCompare += ((n < 10) ? '0' : '') + n;
                     s += dt.getFullYear()
                     n = dt.getDate();
                     sCompare += ((n < 10) ? '0' : '') + n;
//                     console.log(s);
//                     jQuery('#" . Html::getInputId($model, 'reasonchange') . "').attr('data-old', s);
                     jQuery('#" . Html::getInputId($model, 'task_reasonchanges') . "').attr('data-old', s);
//                     if( (s != '" . $model->task_actualtime . "' && ".($model->isNewRecord ? 'false' : 'true').") || ".(strlen($model->task_reasonchanges) > 0 ? 'true' : 'false')." ) {
                     if( ".($bCanChange ? "false" : "true")." ) {
                         if( (sCompare > '" . preg_replace('|(\\d+)\\.(\\d+)\\.(\\d+)|', '${3}${2}${1}', $model->task_actualtime) . "' && ".($model->isNewRecord ? 'false' : 'true').") || ".(strlen($model->task_reasonchanges) > 0 ? 'true' : 'false')." ) {
                            ob.show();
    //                        console.log('need show');
                         }
                         else {
                            ob.hide();
    //                        console.log('need hide');
                         }
                     }
                     }",
                        ]
                    ]
                    ) ?>
                <?= '' // $form->field($model, 'reasonchange', ['options' => ['style' => 'display: none;', 'class' => "form-group field-tasklist-reasonchange"]])->textarea(['rows' => 2, 'data-old'=>$model->isNewRecord ? '' : $model->_oldAttributes['task_actualtime'], ]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-8">
        <div class="col-sm-2">&nbsp;</div>
        <div class="col-sm-3">
            <div class="form-group">
                <?= Html::submitButton('Сохранить изменения', ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <?php
    // $model->isNewRecord ? 'Создать' :
    if( $model->task_reasonchanges != '' ) {
        $a = explode("\n", $model->task_reasonchanges);
        foreach($a As $v) {
            $v = trim($v);
            if( $v == '' ) {
                continue;
            }
            $aPart = explode("\t", $v);
        }
    }
    ?>

</div>
