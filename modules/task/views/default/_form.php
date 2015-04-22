<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use yii\web\View;

use app\modules\user\models\Department;
use app\modules\task\models\Tasklist;
use kartik\date\DatePicker;
use yii\web\JsExpression;

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
    ]);

    $bFinished = ($model->task_progress == Tasklist::PROGRESS_FINISH);
    $bEditDates = !$bFinished || Yii::$app->user->can('createUser');

    ?>

    <div class="col-sm-8">
        <?= $form->field($model, 'task_direct', $aTextParam)->textarea(array_merge(['rows' => 2], $bEditDates ? [] : $aDisable)) ?>
        <?= $form->field($model, 'task_name', $aTextParam)->textarea(array_merge(['rows' => 2], $bEditDates ? [] : $aDisable)) ?>
        <?php
        if( !$model->isNewRecord ) {
        ?>
                <?= $form->field($model, 'task_summary', $aTextParam)->textarea(['rows' => 4, 'data-req' => $bFinished ? 1 : 0]) ?>
        <?php
        }
        $sIdProgress = Html::getInputId($model, 'task_progress');
        $sIdSummary = Html::getInputId($model, 'task_summary');
        $nFinish = Tasklist::PROGRESS_FINISH;
        $sJs = <<<EOT
var oSelProgress = jQuery("#{$sIdProgress}"),
    oSummary = jQuery("#{$sIdSummary}");
oSelProgress.on(
    "change",
    function(event) {
        if( jQuery(this).val() == {$nFinish} ) {
            oSummary.attr("data-req", 1);
        }
        else {
            oSummary.attr("data-req", 0);
        }
    }
);
EOT;
        $this->registerJs($sJs, View::POS_READY, 'changeprogress');

        ?>

        <?= $form->field(
            $model,
            'task_reasonchanges',
            array_merge($aTextParam, ['options' => ['style' => '/*display: none;*/', 'class' => "form-group field-tasklist-reasonchange"]])
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
                         n = dt.getDate(),
                         ob = jQuery('.field-tasklist-reasonchange');
//                     console.log('ob = ', ob);
                     s += ((n < 10) ? '0' : '') + n + '.';
                     n = dt.getMonth() + 1;
                     s += ((n < 10) ? '0' : '') + n + '.';
                     s += dt.getFullYear()
//                     console.log(s);
//                     jQuery('#" . Html::getInputId($model, 'reasonchange') . "').attr('data-old', s);
                     jQuery('#" . Html::getInputId($model, 'task_reasonchanges') . "').attr('data-old', s);
                     if( s != '" . $model->task_actualtime . "' && ".($model->isNewRecord ? 'false' : 'true')." ) {
//                        ob.show();
//                        console.log('need show');
                     }
                     else {
//                        ob.hide();
//                        console.log('need hide');
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
