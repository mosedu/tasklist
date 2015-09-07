<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\cron\models\Crontab */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="crontab-form">

    <?php $form = ActiveForm::begin([
        //
    ]); ?>

    <div class="col-sm-1">
        <label class="control-label" for="crontab-cron_isactive"><?= $model->getAttributeLabel('cron_isactive') ?></label>
        <?= $form->field($model, 'cron_isactive')->checkbox(['label' => '',]) // textInput() ?>
    </div>

    <div class="col-sm-1">
        <?= $form->field($model, 'cron_min')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-sm-1">
        <?= $form->field($model, 'cron_hour')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-sm-1">
        <?= $form->field($model, 'cron_day')->textInput(['maxlength' => true])->hint('месяца') ?>
    </div>

    <div class="col-sm-1">
        <?= $form->field($model, 'cron_wday')->textInput(['maxlength' => true])->hint('недели') ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'cron_path')->textInput(['maxlength' => true]) ?>
    </div>

    <?= '' // $form->field($model, 'cron_tstart')->textInput() ?>

    <?= '' // $form->field($model, 'cron_tlast')->textInput() ?>

    <div class="col-sm-3">
        <?= $form->field($model, 'cron_comment')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-12">
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
