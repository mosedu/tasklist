<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\SubjectImportForm */
/* @var $form yii\widgets\ActiveForm */

$bAjax = Yii::$app->request->isAjax;
$this->title = 'Импорт данных из Excel';

?>

<div class="subject-form">

    <?php $form = ActiveForm::begin([
        'id' => 'subject-import-form',
        'options' => [
            'enctype' => 'multipart/form-data',
        ],
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
//        'validationUrl' => ['validate', 'id'=>$model->us_id],
        'validateOnSubmit' => true,
        'validateOnChange' => false,
        'validateOnBlur' => false,
        'validateOnType' => false,
        /* ********************** bootstrap options ********************** */
//        'layout' => 'horizontal',
    ]); ?>

    <?= $form
            ->field($model, 'xlsfile')
            ->fileInput()
            ->hint('Допустимы файлы с расширением xls, xlsx размером не более 1Мб. <br />Данные будут импортироваться из первого непустого столбца с данными до появления пустой ячейки.')
    ?>


    <div class="form-group">
        <?= Html::submitButton('Импортировать', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Отмена', '', ['class' => 'btn btn-default', 'id' => "{$form->options['id']}-cancel"]) ?>

        <div class="clearfix"></div>
    </div>


    <?php ActiveForm::end(); ?>


</div>

<?php
$formId = $form->options['id'];

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

$this->registerJs($sJs, View::POS_READY, 'submit_resource_form');
