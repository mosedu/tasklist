<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use app\modules\user\models\Department;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin([
        'id' => 'message-form',
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
    ]); ?>

    <?php
    /*
    <?= $form->field($model, 'us_active')->textInput() ?>
    <?= $form->field($model, 'us_password_hash')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'us_logintime')->textInput() ?>
    <?= $form->field($model, 'us_createtime')->textInput() ?>
    <?= $form->field($model, 'us_auth_key')->textInput(['maxlength' => 32]) ?>
    <?= $form->field($model, 'us_email_confirm_token')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'us_password_reset_token')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'us_login')->textInput(['maxlength' => 255]) ?>
     */
    ?>

    <div class="col-sm-4">
        <?= $form->field($model, 'us_lastname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'us_name')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'us_secondname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-4">
        <?= $form->field($model, 'us_email')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'us_workposition')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'us_role_name')->dropDownList(User::getWorkerRoles()) ?>
    </div>

    <?php  if( Yii::$app->user->can(User::ROLE_ADMIN) ) { ?>
    <div class="col-sm-4">
        <?= $form->field($model, 'us_dep_id')->dropDownList(Department::getList(false))  ?>
    </div>
    <?php  } ?>

    <div class="clearfix"></div>

    <div class="col-sm-4">
    <div class="form-group">
        <div class="col-sm-9 col-sm-offset-3">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
