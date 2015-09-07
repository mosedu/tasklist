<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\modules\cron\models\Crontab */

$this->title = 'Новая задача';
$this->params['breadcrumbs'][] = ['label' => 'Crontabs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crontab-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
