<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Worker */

$this->title = $model->worker_id;
$this->params['breadcrumbs'][] = ['label' => 'Workers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="worker-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->worker_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->worker_id], [
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
            'worker_id',
            'worker_task_id',
            'worker_us_id',
        ],
    ]) ?>

</div>
