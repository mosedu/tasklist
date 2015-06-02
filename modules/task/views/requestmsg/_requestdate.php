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
    ]); ?>

    <div class="col-sm-6">
        <?= $form
                ->field($model, 'req_text')
                ->textarea(['rows' => 5])
                ->hint('Текущая дата окончания задачи: ' . date('d.m.Y', strtotime($model->task->task_finishtime)) . '<br />Задача: ' . Html::encode($model->task->task_name))
        ?>
    </div>
    <div class="col-sm-6">
        <?= $form
            ->field($model, 'new_finish_date')
            ->widget(
                DatePicker::className(),
                [
                    'type' => DatePicker::TYPE_INLINE,
                    'pluginOptions' => [
                        'endDate' => date('d.m.Y'),
                        'startDate' => $model->task_create_date,
                    ],
                ]
            )
        ?>
    </div>

    <div class="form-group">
        <div class="col-sm-6">
            <?= Html::submitButton('Отправить запрос', ['class' => 'btn btn-success']) ?>
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
$sCss = <<<EOT
#requestmsg-new_finish_date {
    display: none;
}
EOT;

$this->registerCss($sCss);

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
