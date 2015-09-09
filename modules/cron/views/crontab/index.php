<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\cron\models\CrontabSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Crontab';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crontab-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'cron_id',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'cron_isactive',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    return Html::tag('span', '', ['class' => 'glyphicon ' . ($model->cron_isactive == 1 ? 'glyphicon-ok' : 'glyphicon-remove')]);
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'cron_min',
                'filter' => false,
                'header' => 'Время',
                'content' => function ($model, $key, $index, $column) {
                    return $model->getFulltime();
                },
//                'contentOptions' => [
//                    'class' => 'griddate commandcell',
//                ],
            ],

            'cron_path',
            'cron_comment',

            // 'cron_min',
            // 'cron_hour',
            // 'cron_day',
            // 'cron_wday',
            // 'cron_path',
            // 'cron_tstart',
            // 'cron_tlast',
            // 'cron_comment',

//            ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {stat}', // {view}
                'contentOptions' => [
                    'class' => 'commandcell',
//                    'style' => 'position: relative;',
                ],
                'buttons'=>[
                    'update'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Изменить']);
                    },
                    'stat'=>function ($url, $model) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-new-window"></span>',
                            ['cron/crontab/stat', 'id' => $model->cron_id],
                            ['title' => 'Статистика', 'class'=>'showinmodal']);
                    },
                ],

            ]
        ],
    ]); ?>

</div>
