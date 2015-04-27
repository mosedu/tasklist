<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\modules\task\models\Tasklist;
/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */

$this->title = $model->task_name;
//$this->params['breadcrumbs'][] = ['label' => 'Tasklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
/* <h1><?= Html::encode(Yii::$app->request->isAjax && (mb_strlen($model->task_name, 'UTF-8') > 48) ? mb_substr($model->task_name, 0, 48, 'UTF-8') : $this->title) ?></h1> */


?>
<div class="tasklist-view">

    <?php
    $aAttributes = [
//            'task_id',
//        'task_num',
//            'task_type',
        [
            'attribute' => 'task_num',
            'value' => $model->getTasknum(),
        ],
        [
            'attribute' => 'task_type',
            'value' => $model->getTaskType() . ' / ' . $model->getTaskProgress(),
        ],
        [
            'attribute' => 'task_dep_id',
            'value' => $model->department->dep_name . ' ('.$model->department->dep_shortname.')',
        ],
        'task_direct:ntext',
        'task_name:ntext',
        [
            'attribute' => 'task_createtime',
            'value' => date('d.m.Y', strtotime($model->task_createtime)),
        ],
        [
            'attribute' => 'task_finaltime',
            'value' => date('d.m.Y', strtotime($model->task_finaltime)) . ' / ' . $model->task_actualtime,
            'label' => 'Базовый / ' . ($model->task_progress == Tasklist::PROGRESS_FINISH ? 'Реальный' : 'Новый') . ' сроки',
        ],
//            'task_createtime',
//            'task_finaltime',
//            'task_actualtime',
//        'task_numchanges',
//        'task_reasonchanges:ntext',
//            'task_progress',
//            [
//                'attribute' => 'task_progress',
//                'value' => $model->getTaskProgress(),
//            ],
//            'task_summary:ntext',
    ];

    if( strlen($model->task_summary) > 0 ) {
        $aAttributes = array_merge(
            $aAttributes,
            [
                'task_summary:ntext',
            ]
        );
    }

    if( $model->task_numchanges > 0 ) {
        $aAttributes = array_merge(
            $aAttributes,
            [
                'task_numchanges',
                'task_reasonchanges:ntext',
            ]
        );
    }

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $aAttributes,
    ]);

//    Yii::$app->request->isAjax ? '' :
    ?>

    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->task_id], ['class' => 'btn btn-primary']) ?>
        <?= true ? '' : Html::a('Delete', ['delete', 'id' => $model->task_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

</div>
