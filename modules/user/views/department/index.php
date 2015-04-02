<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\user\models\Department;
use app\modules\user\models\User;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Отделы';
$this->params['breadcrumbs'][] = $this->title;

Yii::info('Grid: ' . print_r(User::getUserRoles(), true));

?>
<div class="department-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить отдел', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

//            'dep_id',
            'dep_name',
            'dep_shortname',
//            'dep_active',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'dep_user_roles',
                'filter' => User::getUserRoles(),
                'content' => function ($model, $key, $index, $column) {
                    /** @var  Department $model */
                    return Html::encode(User::getRoleTitle($model->dep_user_roles));
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'dep_active',
                'filter' => Department::getDepStatuses(),
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->getDepStatus());
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
