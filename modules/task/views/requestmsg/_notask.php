<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;


/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Requestmsg */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="requestmsg-form">

    <div class="col-sm-6">
        Ошибка - не найдена нужная задача
    </div>


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
