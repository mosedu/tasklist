<?php

use yii\helpers\Html;
use yii\grid\GridView;

use app\assets\GriddataAsset;
use app\modules\task\models\Tasklist;

GriddataAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\TasklistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = (Yii::$app->user->identity->department !== null) ? Yii::$app->user->identity->department->dep_name : 'Задачи';
$this->params['breadcrumbs'][] = $this->title;

$aColumns = [
//            ['class' => 'yii\grid\SerialColumn'],
//            'task_id',
//            'task_dep_id',
    // 'task_num',
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_num',
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            $sGlyth = $model->task_type == Tasklist::TYPE_PLAN ? 'calendar' : 'flash';
            $sColor = '#999999';
            $nt1 = strtotime($model->task_finaltime);
            $ntcur = time();
            if( $nt1 < $ntcur ) {
                $sColor = '#ff9999';
                if( strtotime($model->task_actualtime) < $ntcur ) {
                    $sColor = '#ff0000';
                }
            }
//                        color: '.$model->.';
            return '<span class="inline"><span class="inline glyphicon glyphicon-'.$sGlyth.'" style=" color: ' . $sColor . '; font-size: 1.25em;"> ' . Html::a(
                $model->task_num,
                ['default/update', 'id'=>$model->task_id],
                ['title' => "Задача " . $model->getTaskType() . ', редактировать']
            ) . '</span></span>';
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],
//            'task_direct:ntext',
//            'task_name:ntext',
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_name',
//                'filter' => Tasklist::getAllTypes(),
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            return Html::encode($model->task_name) . '<span>' . $model->getTaskType() . ', ' . $model->task_direct . '</span>';
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],
    // 'task_type',
    /*            [
                    'class' => 'yii\grid\DataColumn',
                    // 'header' => 'Состояние',
                    'attribute' => 'task_type',
                    'filter' => Tasklist::getAllTypes(),
                    // 'filterOptions' => ['class' => 'gridwidth7'],
                    'content' => function ($model, $key, $index, $column) {
                        return Html::encode($model->getTaskType());
    //                    return '<span class="glyphicon glyphicon-'.$model->flag->fl_glyth.'" style="color: '.$model->flag->fl_glyth_color.'; font-size: 1.25em;"></span>' //  font-size: 1.25em;
    //                    . '<span class="inline">' . $model->flag->fl_sname . '</span>';
                    },
                    'contentOptions' => [
                        'class' => 'griddate',
                    ],
                ],
    */
    // 'task_createtime',
    // 'task_finaltime',
    // 'task_actualtime',
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_finaltime',
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            /*                    $sdop = '';
                                if( $model->task_actualtime != $model->task_finaltime ) {
                                    $sdop .= '<span>' . date('d.m.Y', strtotime($model->task_finaltime)) . (($model->task_numchanges > 0) ? (' <b>[' . $model->task_numchanges . ']</b>') : '') . '</span>';
                                }
            */
            return date('d.m.Y', strtotime($model->task_finaltime)); // . $sdop;
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_actualtime',
//                'header' => 'Новый срок',
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            return date('d.m.Y', strtotime($model->task_actualtime)) . (($model->task_numchanges > 0) ? (' <b>[' . $model->task_numchanges . ']</b>') : '');
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],
    // 'task_numchanges',
    // 'task_reasonchanges:ntext',
    // 'task_progress',
    // 'task_summary:ntext',

    [
        'class' => 'yii\grid\ActionColumn',
        'contentOptions' => [
            'class' => 'commandcell',
        ],
    ],
];

if( Yii::$app->user->can('createUser') ) {
    $aColumns = array_merge(
        [[
            'class' => 'yii\grid\DataColumn',
            'attribute' => 'task_dep_id',
            'filter' => false,
            'content' => function ($model, $key, $index, $column) {
                return Html::encode($model->department->dep_shortname);
            },
            'contentOptions' => [
                'class' => 'griddate',
            ],
        ]],
        $aColumns
    );
}

?>
<div class="tasklist-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить задачу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $aColumns,
    ]); ?>

</div>
