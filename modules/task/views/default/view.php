<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */

$this->title = $model->task_name;
//$this->params['breadcrumbs'][] = ['label' => 'Tasklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasklist-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->task_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->task_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
//            'task_id',
            [
                'attribute' => 'task_dep_id',
                'value' => $model->department->dep_name,
            ],
            'task_num',
            'task_direct:ntext',
            'task_name:ntext',
//            'task_type',
            [
                'attribute' => 'task_type',
                'value' => $model->getTaskType(),
            ],
            'task_createtime',
            'task_finaltime',
            'task_actualtime',
            'task_numchanges',
            'task_reasonchanges:ntext',
//            'task_progress',
            [
                'attribute' => 'task_progress',
                'value' => $model->getTaskProgress(),
            ],
            'task_summary:ntext',
        ],
    ]) ?>

</div>