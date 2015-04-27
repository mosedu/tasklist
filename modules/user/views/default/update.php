<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = 'Изменение пользователя'; // . ' ' . $model->us_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->us_id, 'url' => ['view', 'id' => $model->us_id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
