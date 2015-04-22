<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\user\models\User;
use app\modules\user\models\Department;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Пользователи';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить пользователя', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'us_id',
//            'us_active',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'us_dep_id',
                'filter' => Department::getList(false),
                'content' => function ($model, $key, $index, $column) {
                    /** @var User $model */

                    return ($model->department !== null) ? Html::encode($model->department->dep_shortname) : '';
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'fname',
                'content' => function ($model, $key, $index, $column) {
                    /** @var User $model */
                    return Html::encode($model->getFullName());
                },
            ],
            'us_email:email',
//            'us_password_hash',
//            'us_name',
            // 'us_secondname',
            // 'us_lastname',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'us_role_name',
                'filter' => User::getUserRoles(),
                'content' => function ($model, $key, $index, $column) {
                    /** @var User $model */
                    return Html::encode(User::getRoleTitle($model->us_role_name));
                },
            ],
            /** @var User $model */
/*
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'us_active',
                'filter' => User::getUserStatuses(),
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->getUserStatus());
                },
            ],
*/

            // 'us_login',
            // 'us_logintime',
            // 'us_createtime',
            // 'us_workposition',
            // 'us_auth_key',
            // 'us_email_confirm_token:email',
            // 'us_password_reset_token',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
