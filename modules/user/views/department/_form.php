<?php

use yii\helpers\Html;
// use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveForm;
use app\modules\user\models\User;
use app\modules\user\models\Department;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\Department */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-form">

    <?php $form = ActiveForm::begin([
        'id' => 'message-form',
        'layout' => 'horizontal',
        'options'=>[
//            'enctype'=>'multipart/form-data'
        ],
        'fieldConfig' => [
            'horizontalCssClasses' => [
                'label' => 'col-sm-6',
                'offset' => 'col-sm-offset-6',
                'wrapper' => 'col-sm-6',
                'hint' => 'col-sm-6 col-sm-offset-6',
            ],
        ],
    ]); ?>

    <div class="col-sm-6">
        <?= $form->field($model, 'dep_name')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-6">
        <?= $form->field($model, 'dep_shortname')->textInput(['maxlength' => 255]) ?>
    </div>

    <div class="col-sm-6">
        <?= $form->field($model, 'dep_active')->dropDownList(Department::getDepStatuses()) ?>
    </div>

    <div class="col-sm-6">
        <?= $form->field($model, 'dep_user_roles')->dropDownList(User::getUserRoles()) ?>
    </div>

    <div class="clearfix"></div>

    <div class="col-sm-6">
        <div class="form-group">
            <div class="col-sm-6 col-sm-offset-6">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success btn-block' : 'btn btn-primary btn-block']) ?>
            </div>
        </div>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
