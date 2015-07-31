<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 30.07.2015
 * Time: 17:34
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use app\modules\task\models\AskForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\task\models\AskForm */

?>


<div class="site-contact">
    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin([
    'id' => 'ask-form',
    'enableAjaxValidation' => true,
    'enableClientValidation' => true,
    'validateOnChange' => false,
    'validateOnBlur' => false,
    'options' => [
        'action' => $_SERVER['REQUEST_URI'],
    ],
]); ?>

            <?= $model->text ?>
            <?= $form->field($model, 'pressed', ['template' => "{input}\n{error}"])->hiddenInput() ?>
            <div id="ask-user-message"></div>
<?php
    foreach($model->buttons As $k=>$v) {
        $a = ['class' => 'valuelink', 'id'=>$k];

        if( is_array($v) ) {
            $t = isset($v['text']) ? $v['text'] : '--';
            unset($v['text']);
            foreach($v As $k1 => $v1) {
                if( !$model->isAttribute($k1) ) {
                    continue;
                }
                $a[$k1] = isset($a[$k1]) ? ($a[$k1] . ' ' . $v1) : $v1;
            }
        }
        else {
            $t = $v;
        }
        echo Html::a($t, '#', $a) . ' ';
    }
if( Yii::$app->request->isAjax ) {
    ?>
    <div class="form-group" style="display: none;">
        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button', 'id'=>'submit-askform-button']) ?>
    </div>
<?php
}
?>
<?php ActiveForm::end(); ?>
</div>
</div>

</div>

<?php
if( Yii::$app->request->isAjax ) {
    $pressedId = Html::getInputId($model, 'pressed');
    $sJs = <<<EOT
var oForm = jQuery('#{$form->options['id']}'),
    oDialog = oForm.parents('[role="dialog"]');
jQuery('.valuelink')
.on("click", function (event) {
    console.log('Click');
    event.preventDefault();
    $("#{$pressedId}").val($(this).attr("id"));
    $("#submit-askform-button").trigger("click");
    return false;
});
jQuery('#ask-form')
.on('afterValidate', function (event, messages, deferreds) {
    console.log(event);
    console.log(messages);
    if( "error" in messages ) {
        jQuery("#ask-user-message")
            .addClass("bg-danger")
            .removeClass("bg-success")
            .html(messages.error)
    }
    else {
        if( "js" in messages ) {
            var oFunc = new Function('', messages.js);
            oFunc();
        }
//        oDialog.modal('hide');
    }
})
.on('submit', function (event) {
    event.preventDefault();
    var formdata = oForm.data().yiiActiveForm,
        oRes = jQuery("#formresultarea");

    event.preventDefault();
    if( formdata.validated ) {
        // имитация отправки
        formdata.validated = false;
        formdata.submitting = true;
    }
    return false;
});
EOT;

    $this->registerJs($sJs, View::POS_READY, 'showaskdialog');
}
