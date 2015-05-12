<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\File */

$this->title = 'Update File: ' . ' ' . $model->file_id;
$this->params['breadcrumbs'][] = ['label' => 'Files', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->file_id, 'url' => ['view', 'id' => $model->file_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="file-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
