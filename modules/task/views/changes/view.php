<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Changes */

$this->title = $model->ch_id;
$this->params['breadcrumbs'][] = ['label' => 'Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="changes-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->ch_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->ch_id], [
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
            'ch_id',
            'ch_us_id',
            'ch_task_id',
            'ch_data',
            'ch_text:ntext',
        ],
    ]) ?>

</div>
