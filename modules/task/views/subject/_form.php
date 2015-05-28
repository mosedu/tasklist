<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Subject */
/* @var $form yii\widgets\ActiveForm */

$bAjax = Yii::$app->request->isAjax;
?>

<div class="subject-form">

    <?php $form = ActiveForm::begin([
        'id' => 'subject-edit-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
//        'validationUrl' => ['validate', 'id'=>$model->us_id],
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        /* ********************** bootstrap options ********************** */
//        'layout' => 'horizontal',
    ]); ?>

    <?= $form->field($model, 'subj_title')->textarea(['rows' => 3]) ?>

    <?= '' // $form->field($model, 'subj_created')->textInput() ?>

    <?= '' // $form->field($model, 'subj_dep_id')->textInput() ?>

    <?= '' // $form->field($model, 'subj_comment')->textInput(['maxlength' => true]) ?>

    <?= '' // $form->field($model, 'subj_is_active')->textInput() ?>

    <div class="form-group">
        <div class="col-sm-3">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Отмена', '', ['class' => 'btn btn-default', 'id' => "{$form->options['id']}-cancel"]) ?>
        </div>
        <div class="col-sm-6">
            <div class="alert alert-success" role="alert" id="formresultarea" style="display: none; text-align: center;"></div>
        </div>
        <div class="clearfix"></div>
    </div>


    <?php ActiveForm::end(); ?>


</div>

<?php
$formId = $form->options['id'];

if( $bAjax ) {
    $sJs = <<<EOT
var oForm = jQuery('#{$formId}'),
    oCancel = jQuery('#{$formId}-cancel'),
    oDialog = oForm.parents('[role="dialog"]');

oCancel.on("click", function(event){ event.preventDefault(); oDialog.modal('hide'); return false; });

oForm
    .on('afterValidate', function (event, messages) {
    //    console.log("afterValidate()", event);
    })
    .on('submit', function (event) {
    //    console.log("submit()");
        var formdata = oForm.data().yiiActiveForm,
            oRes = jQuery("#formresultarea");

        event.preventDefault();
        if( formdata.validated ) {
            // имитация отправки
            formdata.validated = false;
            formdata.submitting = true;

            // показываем подтверждение
            oRes
                .text("Данные сохранены")
                .fadeIn(800, function(){
                    setTimeout(
                        function(){
                            oRes.fadeOut(function(){ window.location.reload(); });
                            oDialog.modal('hide');
                        },
                        1000
                    );
                });
        }
        return false;
    });
//console.log("oForm = ", oForm);
EOT;
}
else {
    $sJs = <<<EOT
var oCancel = jQuery('#{$formId}-cancel');

oCancel.on(
    "click",
    function(event){
        event.preventDefault();
        window.history.go(-1);
//        window.history.back();
        return false;
    });

EOT;

}
$this->registerJs($sJs, View::POS_READY, 'submit_resource_form');
