<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
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

    <div class="col-sm-4">
        <?= $model->getFullName()  ?>
    </div>

    <div class="col-sm-4">
        <?= $form->field($model, 'us_role_name')->dropDownList(User::getSelectRoles())  ?>
    </div>

    <div class="col-sm-4">
    <div class="form-group">
        <div class="col-sm-9 col-sm-offset-3">
            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success btn-block']) ?>
        </div>
    </div>
    </div>

    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
