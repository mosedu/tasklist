<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\web\View;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\File */
/* @var $form yii\widgets\ActiveForm */

/*
<div class="file-data" id="filedata">
    <div class="form-group">
        <label class="control-label col-sm-3">Файлы</label>
        <div class="col-sm-9">
            <?= Html::a('Добавить файл', '', ['id' => 'addfilelink', 'class'=>'btn btn-default']) ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
*/

if( !isset($index) ) {
    $index = '';
}
?>

<!-- div class="" -->
    <div class="col-sm-11">
        <?= $form->field($model, '[' . $index . ']filedata', ['template'=>"{input}\n{hint}\n{error}"])->fileInput() ?>
    </div>
    <div class="col-sm-1">
        <?= Html::a(
            Html::tag('span', '', ['class' => 'glyphicon glyphicon-remove']),
            '',
            [
                'class' => 'btn btn-danger remove-file',
            ]
        ) ?>
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, '[' . $index . ']file_id', ['template'=>"{input}"])->hiddenInput() ?>
        <?= $form->field($model, '[' . $index . ']file_comment', ['template'=>"{input}\n{hint}\n{error}"])->textInput(['maxlength' => true]) ?>
    </div>
    <div class="clearfix"></div>
<!-- /div -->

<?php

/*
$sJs = <<<EOT
var oTemplateFile = jQuery("#fileuploadrow"),
    oFileData = jQuery("#filedata");

// oTemplateFile.hide();

jQuery("#addfilelink").on("click", function(event){
    event.preventDefault();
    console.log("click file");
    var oNew = oTemplateFile.clone();
    oNew.attr({id: "id_file_" + (new Date()).getTime()}).appendTo(oFileData).show();
    oNew.find("[type='file']").trigger("click");
    console.log(oNew.find("[type='file']"));
    return false;
});
EOT;
$this->registerJs($sJs, View::POS_READY, 'fileselectscript');
*/
