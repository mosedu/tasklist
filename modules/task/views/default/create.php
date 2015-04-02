<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */

$this->title = 'Добавить задачу';
//$this->params['breadcrumbs'][] = ['label' => 'Задачи', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasklist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
