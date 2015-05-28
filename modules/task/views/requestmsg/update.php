<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Requestmsg */

$this->title = 'Изменение запроса: ' . $model->req_id;
$this->params['breadcrumbs'][] = ['label' => 'Requestmsgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->req_id, 'url' => ['view', 'id' => $model->req_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="requestmsg-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render($form, [ // '_form'
        'model' => $model,
    ]) ?>

</div>
