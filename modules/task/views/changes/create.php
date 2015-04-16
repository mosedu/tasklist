<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Changes */

$this->title = 'Create Changes';
$this->params['breadcrumbs'][] = ['label' => 'Changes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="changes-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
