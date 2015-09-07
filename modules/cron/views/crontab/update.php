<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\cron\models\Crontab */

$this->title = 'Изменение задачи: ' . $model->cron_id;
$this->params['breadcrumbs'][] = ['label' => 'Crontabs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cron_id, 'url' => ['view', 'id' => $model->cron_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="crontab-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
