<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\task\models\Tasklist;
use app\modules\task\models\Action;
use kartik\date\DatePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\ActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Лог задач';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="action-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'act_id',
//            'act_us_id',
//            'act_type',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'act_type',
                'filter' => Action::getAllTypes(),
                'content' => function ($model, $key, $index, $column) {
                    $a = [
                        'question-sign',
                        'plus',
                        'pencil',
                        'trash',
                    ];

                    return '<span class="glyphicon glyphicon-'.$a[$model->act_type].'"></span>'; // $model->getTypeText($model->act_type);
                },
            ],
            // 'act_createtime',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'act_createtime',
//                'filter' => false,
                'filter' => DatePicker::widget([
                    'type' => DatePicker::TYPE_INPUT,
                    'attribute' => 'act_createtime',
                    'model' => $searchModel,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose'=>true,
                    ]
                ]),
                'content' => function ($model, $key, $index, $column) {
                    return date('d.m.Y H:i', strtotime($model->act_createtime));
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'act_data',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    $sData = empty($model->act_data) ? '' : Tasklist::getChangesLogText(unserialize($model->act_data));
                    return Html::a(Html::encode($model->task->task_name), $model->task->getUrl()) . ' (' . Html::encode($model->task->department->dep_name) . ') ' . ($sData != '' ? ('<br />' . $sData) : '') ;
                },
//                'contentOptions' => function ($model, $key, $index, $column) {
//                    $diff = date('Ymd', strtotime($model->task_finaltime)) - date('Ymd', strtotime($model->task_actualtime));
//                    return [
//                        'class' => 'griddate' . (($model->task_progress == Tasklist::PROGRESS_FINISH) ? (( $diff < 0 ) ? ' colorcell_red' : (( $diff > 0 ) ? ' colorcell_green' : '')) : ''),
//                    ];
//                },
            ],

            // 'act_data:ntext',
            // 'act_table',
            // 'act_table_pk',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);

    $sJs = <<<EOT
jQuery('input[name="ActionSearch[act_createtime]"]').
EOT;

    ?>

</div>
