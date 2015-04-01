<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */

$this->title = 'Create Tasklist';
$this->params['breadcrumbs'][] = ['label' => 'Tasklists', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tasklist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
