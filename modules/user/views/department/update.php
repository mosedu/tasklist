<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\Department */

$this->title = /*'Изменить ' . ' ' . */ $model->dep_name;
$this->params['breadcrumbs'][] = ['label' => 'Отделы', 'url' => ['index']];
//$this->params['breadcrumbs'][] = ['label' => $model->dep_shortname, 'url' => ['view', 'id' => $model->dep_id]];
$this->params['breadcrumbs'][] = 'Изменить';
?>
<div class="department-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
