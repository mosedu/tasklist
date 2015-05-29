<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Requestmsg */
/* @var $form yii\widgets\ActiveForm */

$bAjax = Yii::$app->request->isAjax;

?>

<div class="requestmsg-form">

    <?php $form = ActiveForm::begin([
        'id' => 'request-edit-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
//        'validationUrl' => ['validate', 'id'=>$model->us_id],
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        /* ********************** bootstrap options ********************** */
//        'layout' => 'horizontal',
    ]);
    $aData = unserialize($model->req_data);
    ?>

    <div class="col-sm-12">
        <p><strong>Запрос:</strong> <?= Html::encode($model->req_text) ?><p>
        <p><strong>От:</strong> <?= Html::encode($model->user->getFullname()) ?><p>
        <p><strong>Задача:</strong> <?= Html::encode($model->task->task_name) ?><p>
        <p><strong>Дата окончания:</strong> <?= date('d.m.Y', strtotime($model->task->task_finishtime)) ?> -&gt; <?= date('d.m.Y', strtotime($aData['task_finishtime'])) ?><p>
        <?=  $form->field($model, 'req_is_active', ['template' => '{input}'])->hiddenInput() ?>
    </div>
    <div class="col-sm-12">
    </div>

    <div class="form-group">
        <div class="col-sm-6">
            <?= Html::submitButton('Принять', ['class' => 'btn btn-success setactiveval', 'data-val' => 0]) ?>
            <?= Html::submitButton('Отклонить', ['class' => 'btn btn-danger setactiveval', 'data-val' => 1]) ?>
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

$sId = Html::getInputId($model, 'req_is_active');
$sJs .= <<<EOT

    jQuery(".setactiveval").on("click", function(event){
        var ob = jQuery(this),
            nval = ob.attr("data-val");
        jQuery("#{$sId}").val(nval);
    });

EOT;
$this->registerJs($sJs, View::POS_READY, 'submit_resource_form');
