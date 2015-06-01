<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\modules\user\models\Department;
use kartik\date\DatePicker;
use yii\helpers\Url;
use yii\web\View;
use app\modules\user\models\DateIntervalForm;


/* @var $this yii\web\View */
/* @var $model app\modules\user\models\DateIntervalForm */
/* @var $form yii\widgets\ActiveForm */

if( $model->from_date == '' ) {
    $model->from_date = date('01.m.Y');
}
if( $model->to_date == '' ) {
    $model->to_date = date('d.m.Y');
}

$aDisable = [
    'disabled' => true,
];

$aDepartment = Department::getList(false);
if( Yii::$app->user->can('createUser') || (Yii::$app->user->identity->us_dep_id == 1) ) {
    $a = [""=>"ГАУ \"ТемоЦентр\""];
    foreach($aDepartment As $k=>$v) {
        $a[$k] = $v;
    }
    $aDepartment = $a;
}

$dNow = mktime(0, 0, 0, date('m'), 1, date('Y'));
$dPrevMonth = mktime(0, 0, 0, date('m', $dNow - 1), 1, date('Y', $dNow - 1));

$aPeriods = [
    [
        'title' => 'Текущий месяц',
        'from' => date('d.m.Y', $dNow),
        'to' => date('d.m.Y'),
    ],
    [
        'title' => 'Предыдущий месяц',
        'from' => date('d.m.Y', $dPrevMonth),
        'to' => date('d.m.Y', $dNow-1),
    ],
/*

    [
        'title' => 'Текущий квартал',
        'from' => '',
        'to' => '',
    ],
    [
        'title' => 'Предыдущий квартал',
        'from' => '',
        'to' => '',
    ],
    [
        'title' => 'Текущее полугодие',
        'from' => '',
        'to' => '',
    ],
    [
        'title' => 'Предыдущее полугодие',
        'from' => '',
        'to' => '',
    ],
    [
        'title' => 'Текущий год',
        'from' => '',
        'to' => '',
    ],
    [
        'title' => 'Предыдущий год',
        'from' => '',
        'to' => '',
    ],
*/
];


?>

<div class="dateinterval-form">

    <?php $form = ActiveForm::begin([
        'id' => 'dateinterval-form',
        'options'=>[
//            'enctype'=>'multipart/form-data'
        ],
    ]); ?>

    <?php
    /*
    <?= $form->field($model, 'us_active')->textInput() ?>
    <?= $form->field($model, 'us_password_hash')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'us_logintime')->textInput() ?>
    <?= $form->field($model, 'us_createtime')->textInput() ?>
    <?= $form->field($model, 'us_auth_key')->textInput(['maxlength' => 32]) ?>
    <?= $form->field($model, 'us_email_confirm_token')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'us_password_reset_token')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'us_login')->textInput(['maxlength' => 255]) ?>
     */
    ?>

    <div class="col-sm-12">
        <?= '' // $form->field($model, 'us_lastname')->textInput(['maxlength' => 255]) ?>
    </div>
    <div class="col-sm-12">
        <?= '' // $form->field($model, 'us_lastname')->textInput(['maxlength' => 255]) ?>
    </div>
    <div class="form-group">
        <div class="col-sm-6">
            <?php


            echo '<label class="control-label">Период</label>';
            echo DatePicker::widget([
                'model' => $model,
                'attribute' => 'from_date',
                'attribute2' => 'to_date',
                'options' => ['placeholder' => $model->getAttributeLabel('from_date')],
                'options2' => ['placeholder' => $model->getAttributeLabel('to_date')],
                'type' => DatePicker::TYPE_RANGE,
                'form' => $form,
                'separator' => '...',
                'pluginOptions' => [
                    'format' => 'dd.mm.yyyy',
                    'autoclose' => true,
                    'endDate' => date('d.m.Y'),
                ]
            ]);

            ?>
            <?php
            echo '<div class="form-group"><label class="control-label">&nbsp;</label><div class="btn-group">';
            echo '<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-expanded="false">';
            echo 'Периоды <span class="caret"></span>';
            echo '</button>';
            echo '<ul class="dropdown-menu" role="menu">';
/*
    <li><a href="#">Action</a></li>
    <li><a href="#">Another action</a></li>
    <li><a href="#">Something else here</a></li>
    <li class="divider"></li>
    <li><a href="#">Separated link</a></li>
*/
            foreach($aPeriods As $v) {
                echo '<li><a href="" class="setperiod" data-from="'.$v['from'].'" data-to="'.$v['to'].'">'.Html::encode($v['title']).'</a></li>';
            }
            echo '</ul></div>';
            echo '</div>';
            ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'department_id')->dropDownList($aDepartment, (Yii::$app->user->can('createUser') || (Yii::$app->user->identity->us_dep_id == 1)) ? [] : $aDisable) ?>
            <?= $form->field($model, 'user_id')->dropDownList([], (Yii::$app->user->can('createUser') || (Yii::$app->user->identity->us_dep_id == 1) || Yii::$app->user->can('department') ) ? [] : $aDisable) ?>
        </div>
        <div class="clearfix"></div>
    </div>


    <div class="col-sm-4">
        <div class="form-group">
            <?= Html::submitButton('Рассчитать' , ['class' => 'btn btn-success btn-lg']) ?>
        </div>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$sDepartmentId = Html::getInputId($model, 'department_id');
$sUserId = Html::getInputId($model, 'user_id');
$UserId = $model->user_id;
$sUrl = Url::to(['/user/worker/list'], true);
$sDateFromId = Html::getInputId($model, 'from_date');
$sDateToId = Html::getInputId($model, 'to_date');

$sJs = <<<EOT
    var setUsers = function() {
        var oDep = jQuery("#{$sDepartmentId}"),
            depId = oDep.val(),
            oldVal = "{$UserId}";
        jQuery.ajax({
            dataType: "json",
            url: "{$sUrl}",
//            method: "POST",
            data: {id: depId},
            success: function(data, textStatus, jqXHR) {
                jQuery("#{$sUserId} option").remove();
                var olist = jQuery("#{$sUserId}");
                olist.append(jQuery('<option>').text("").attr('value', ""));
                for(var i in data) {
                    var oOpt = jQuery('<option>').text(data[i]).attr('value', i);
//                    if( i == oldVal ) {
//                        console.log("op["+i+"] ("+oldVal+")");
//                        oOpt.attr("selected", true);
//                    }
                    olist.append(oOpt);
                }

                if( jQuery("#{$sUserId} option[value=\""+oldVal+"\"]").length > 0 ) {
                    jQuery("#{$sUserId}").val(oldVal).trigger("change");
//                    console.log("Set value: to #{$sUserId} val("+oldVal+")");
                }
            }
        });
    };

    jQuery("#{$sDepartmentId}").on(
        "change",
        function(event) {
            setUsers();
        }
    );
    setUsers();

    jQuery(".setperiod")
        .on(
            "click",
            function(event){
                var oLink = jQuery(this);
                event.preventDefault();
                jQuery("#{$sDateFromId}").val(oLink.attr("data-from"));
                jQuery("#{$sDateToId}").val(oLink.attr("data-to"));
                console.log(oLink.attr("data-from") + " - " + oLink.attr("data-to"));
                jQuery('.btn-group.open .dropdown-toggle').dropdown('toggle');
                return false;
            }
        );
EOT;

$this->registerJs($sJs, View::POS_READY, 'deteinterval');

/*



*/
/*
            'url' => $sUrl,
            'dataType' => 'json',
            'data' => new JsExpression('function(params) { return {id: jQuery("#'.$sDepartmentId.'").val(), type: "select2"}; }')

*/
