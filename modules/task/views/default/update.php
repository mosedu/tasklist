<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */

$this->title = 'Изменение задачи ' . $model->getTasknum() . ': ' . mb_strimwidth($model->task_name, 0, 30, ' ...', 'UTF-8') ;
// $this->params['breadcrumbs'][] = ['label' => 'Tasklists', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->task_id, 'url' => ['view', 'id' => $model->task_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasklist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
