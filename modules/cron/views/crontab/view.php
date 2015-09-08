<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\web\View;

use app\modules\cron\models\Crontab;

/* @var $this yii\web\View */
/* @var $model app\modules\cron\models\Crontab */

$this->title = $model->cron_id;
$this->params['breadcrumbs'][] = ['label' => 'Crontabs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crontab-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <!-- p>
        <?= '' // Html::a('Update', ['update', 'id' => $model->cron_id], ['class' => 'btn btn-primary']) ?>
        <?= '' /* Html::a('Delete', ['delete', 'id' => $model->cron_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ])*/ ?>
    </p -->

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'cron_id',
            'cron_min',
            'cron_hour',
            'cron_day',
            'cron_wday',
            'cron_path',
            'cron_tstart',
            'cron_tlast',
            'cron_comment',
            'cron_isactive',
        ],
    ]) ?>

    <?php

//    echo $model->getFulltime(' \ ') . ' = ' . nl2br(print_r($model->getTime(), true));



/*

    echo $model->cron_min . ' = ' . nl2br(print_r($model->getPeriodValues($model->cron_min, $model->aIntervals['cron_min'][0], $model->aIntervals['cron_min'][1]), true));
    echo $model->cron_hour . ' = ' . nl2br(print_r($model->getPeriodValues($model->cron_hour, $model->aIntervals['cron_hour'][0], $model->aIntervals['cron_hour'][1]), true));
    echo $model->cron_day . ' = ' . nl2br(print_r($model->getPeriodValues($model->cron_day, $model->aIntervals['cron_day'][0], $model->aIntervals['cron_day'][1]), true));
    echo $model->cron_wday . ' = ' . nl2br(print_r($model->getPeriodValues($model->cron_wday, $model->aIntervals['cron_wday'][0], $model->aIntervals['cron_wday'][1]), true));
*/
    ?>

</div>
