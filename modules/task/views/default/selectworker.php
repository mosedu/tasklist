<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\ArrayHelper;

use app\modules\task\models\Tasklist;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */
/* @var $form yii\widgets\ActiveForm */

$aDisable = [];
$bFinished = ($model->task_progress == Tasklist::PROGRESS_FINISH);

if( $bFinished ) {
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
        'id' => 'setworker-form',
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
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
    ]);

    ?>

    <div class="col-sm-8">
    <?= $form->field($model, 'task_worker_id')->dropDownList(
        ArrayHelper::map(User::getDepartmentWorker($model->task_dep_id), 'us_id', function($model){ return $model->getFullName(); }),
        $aDisable
    ) ?>
    </div>
    <div class="clearfix"></div>


    <div class="col-sm-8">
        <div class="form-group">
        <div class="col-sm-3">&nbsp;</div>
        <div class="col-sm-4">
                <?= Html::submitButton('Назначить сотрудника', ['class' => 'btn btn-primary btn-block']) ?>
        </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <div class="clearfix"></div>


</div>

<?php
$sJs = <<<EOT
var nCou = 0;
jQuery("#setworker-form")
    .on('submit', function(event) {
        jQuery("#messagedata").modal('hide');
        if( nCou > 0 ) {
            window.location.reload();
        }
        console.log('Submit ' + nCou);
        nCou++;
        event.preventDefault();
        return false;
    });
EOT;

$this->registerJs($sJs, View::POS_READY);


