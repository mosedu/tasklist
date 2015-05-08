<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use app\modules\main\models\MessageForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\modules\main\models\MessageForm */

?>
<div class="site-contact">
    <div class="row">
        <div class="col-lg-12">
            <?php $form = ActiveForm::begin([
                'id' => 'message-form',
                'enableAjaxValidation' => true,
                'enableClientValidation' => true,
                'validateOnChange' => false,
                'validateOnBlur' => false,
                'options' => [
                    'action' => $_SERVER['REQUEST_URI'],
                ],
            ]); ?>
                <?= $form->field($model, 'body')->textArea(['rows' => 6]) ?>
            <?php
                if( Yii::$app->request->isAjax ) {
            ?>
                    <div class="form-group" style="display: none;">
                        <?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button', 'id'=>'submit-message-button']) ?>
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
    $sJs = <<<EOT
jQuery('#sendmessage')
.on('click', function (event) {
    console.log('Click');
//    jQuery('#message-form').yiiActiveForm("validate");
    $('#submit-message-button').trigger('click');
});
jQuery('#message-form')
.on('afterValidate', function (event, messages, deferreds) {
    console.log(event);
    console.log(messages);
})
.on('submit', function (event) {
    event.preventDefault();
    return false;
});
EOT;

    $this->registerJs($sJs, View::POS_READY, 'showmessagedialog');
}
