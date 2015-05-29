<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\RequestmsgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Переносы дат';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requestmsg-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php /*
    <p>
        <?= Html::a('Create Requestmsg', ['create'], ['class' => 'btn btn-success']) ?>
    </p> */
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'req_id',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'req_user_id',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    return $model->user->getFullname();
                },
                'contentOptions' => function ($model, $key, $index, $column) {
                    return [
                        'class' => 'griddate' . ($model->req_is_active ? '' : ' nonactve'),
                    ];
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'req_task_id',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    $s = $model->task->getTasknum() . ' '. Html::encode($model->task->task_name);
                    return Html::a(
                        $s,
                        ['/task/default/view', 'id'=>$model->task->task_id],
                        ['title' => $s, 'class' => 'showinmodal' . ($model->req_is_active ? '' : ' nonactve')]
                    );
                },
                'contentOptions' => [
                    'class' => 'griddate',
                ],
            ],


//            'req_user_id',
//            'req_task_id',
//            'req_text',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'req_text',
                'filter' => false,
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->req_text);
                },
                'contentOptions' => function ($model, $key, $index, $column) {
                    return [
                        'class' => 'griddate' . ($model->req_is_active ? '' : ' nonactve'),
                    ];
                },
            ],

//            'req_comment',
            // 'req_created',
            // 'req_data:ntext',
            // 'req_is_active',

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update}', // . (Yii::$app->user->can('createUser') ? ' {delete} {undelete}' : '') . (Yii::$app->user->can('department') ? ' {findate}' : ''), // {view}  {answer} {toword}
                'contentOptions' => [
                    'class' => 'commandcell',
                ],
                'buttons'=>[
                    'update'=>function ($url, $model) {
                        return $model->req_is_active ? Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Обработать запрос', 'class'=>'showinmodal', ]) : '';
                    },
                    'findate'=>function ($url, $model) {
                        $bFinish = ($model->task_progress == Tasklist::PROGRESS_FINISH);
                        return $bFinish ? Html::a( '<span class="glyphicon glyphicon-new-window"></span>', ['requestmsg/create', 'taskid' => $model->task_id], ['title' => 'Запрос на перенос даты окончания задачи', 'class'=>'showinmodal']) : '';
                    },
                    'delete' => function ($url, $model, $key) {
                        /** @var Tasklist $model */
                        return
                            ( $model->task_active == Tasklist::STATUS_DELETED ) ?
                                '' :
                                Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => Yii::t('yii', 'Delete'),
                                    'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                    'data-method' => 'post',
                                    'data-pjax' => '0',
                                ]);
                    },
                    'undelete' => function ($url, $model, $key) {
                        /** @var Tasklist $model */
                        return
                            ( $model->task_active == Tasklist::STATUS_DELETED ) ?
                                Html::a(
                                    '<span class="glyphicon glyphicon-share-alt"></span>',
                                    $url,
                                    [
                                        'title' => 'Восстановить',
                                    ])
                                : '';
                    },

                ],

            ]


//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);

    $sCss = <<<EOT
.nonactve {
    color: #aaaaaa;
}
EOT;

    $this->registerCss($sCss);

    ?>

</div>
