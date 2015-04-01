<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\TasklistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Tasklists';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasklist-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Tasklist', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'task_id',
            'task_dep_id',
            'task_num',
            'task_direct:ntext',
            'task_name:ntext',
            // 'task_type',
            // 'task_createtime',
            // 'task_finaltime',
            // 'task_actualtime',
            // 'task_timechanges:datetime',
            // 'task_reasonchanges:ntext',
            // 'task_progress',
            // 'task_summary:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
