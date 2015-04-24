<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = $model->us_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <!-- h1><?= Html::encode($this->title) ?></h1 -->

    <!-- p>
        <?= '' // Html::a('Update', ['update', 'id' => $model->us_id], ['class' => 'btn btn-primary']) ?>
        <?= '' /* Html::a('Delete', ['delete', 'id' => $model->us_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p -->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'us_id',
//            'us_active',
            'us_email:email',
//            'us_password_hash',
//            'us_lastname',
//            'us_name',
//            'us_secondname',
            [
                'label' => 'ФИО',
                'value' => $model->getFullName(),
            ],
            [
                'label' => 'Отдел',
                'value' => $model->department->dep_name . ' (' . $model->department->dep_shortname . ')',
            ],

//            'us_login',
//            'us_logintime',
//            'us_createtime',
            'us_workposition',
//            'us_auth_key',
//            'us_email_confirm_token:email',
//            'us_password_reset_token',
        ],
    ]) ?>

</div>
