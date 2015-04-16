<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Changes */

$this->title = 'Update Changes: ' . ' ' . $model->ch_id;
$this->params['breadcrumbs'][] = ['label' => 'Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ch_id, 'url' => ['view', 'id' => $model->ch_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="changes-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
