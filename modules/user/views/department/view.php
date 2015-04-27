<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\Department */

$this->title = $model->dep_shortname;
$this->params['breadcrumbs'][] = ['label' => 'Departments', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

/*
    <p>
        <?= Html::a('Update', ['update', 'id' => $model->dep_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->dep_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>


*/
?>
<div class="department-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'dep_id',
            'dep_name',
            [
                'attribute' => 'dep_user_roles',
                'value' => User::getRoleTitle($model->dep_user_roles),
            ],
//            'dep_shortname',
//            'dep_active',
        ],
    ]) ?>

</div>
