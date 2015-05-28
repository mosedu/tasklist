<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Requestmsg */

$this->title = 'Create Requestmsg';
$this->params['breadcrumbs'][] = ['label' => 'Requestmsgs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requestmsg-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
