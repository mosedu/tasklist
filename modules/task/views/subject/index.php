<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\SubjectSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Направления';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать направление', ['create'], ['class' => 'btn btn-success showinmodal', 'title' => 'Новое направление']) ?>
        <?= Html::a('Импорт направлений из Excel', ['import'], ['class' => 'btn btn-success', 'title' => 'Импорт направлений']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'subj_id',
            'subj_title:ntext',
//            'subj_created',
//            'subj_dep_id',
//            'subj_comment',
            // 'subj_is_active',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'subj_is_active',
                'content' => function ($model, $key, $index, $column) {
                    /** @var Resource $model */
                    return '<span class="glyphicon glyphicon-' . ($model->subj_is_active == 1 ? 'ok' : 'remove') . '"></span>';
                },
                'contentOptions' => [
                    'class' => 'griddate grig-active-column',
                ],
                'headerOptions' => [
                    'class' => 'grig-active-column',
                ],
                'filter' => [0 => 'Удалено', 1 => 'Активно', ],
            ],

//            ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => [
                    'class' => 'grig-active-column',
                ],
                'contentOptions' => [
                    'class' => 'commandcell grig-active-column',
                ],
                'template' => '{update} {delete}', // {view}  {update}
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', ['user/view', 'id'=>$model->us_id], // $url,
                            ['title' => Html::encode($model->fname), 'class'=>'btn btn-success showinmodal']); // , 'data-pjax' => '0' primary
//                            ['title' => Yii::t('yii', 'View'), 'class'=>'showinmodal']); // , 'data-pjax' => '0'
                    },
                    /*
                            'update'=>function ($url, $model) {
                                return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', ['alldata/update', 'id'=>$model->us_id], ['title' => 'Изменить', 'class' => 'btn btn-success row_edit_link showinmodal', 'id'=>'editlink_' . $model->us_id]);
                    //            return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Изменить', 'class' => 'btn btn-success row_edit_link showinmodal', 'id'=>'editlink_' . $model->us_id]);
                            },
                    */
                    'update'=>function ($url, $model) {
//            return Html::a( '<span class="glyphicon glyphicon-user"></span>', ['user/update', 'id'=>$model->us_id], // $url,
                        return Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, // ['resource/edit', 'id'=>$model->res_id],
                            ['title' => 'Изменить направление', 'class'=>'showinmodal']); // , 'data-pjax' => '0' primary
//                            ['title' => Yii::t('yii', 'View'), 'class'=>'showinmodal']); // , 'data-pjax' => '0'
                    },
                            'delete' => function ($url, $model, $key) {
                                return
                                    Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                        'title' => Yii::t('yii', 'Delete'),
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'data-pjax' => '0',
                                    ]);
                            },
                    /*
                            'unlink' => function ($url, $model, $key) {
                                if( Yii::$app->user->can(User::ROLE_ADMIN) ) {
                                    return
                                        Html::a('<span class="glyphicon glyphicon-erase"></span>', $url, [
                                            'title' => Yii::t('yii', 'unlink'),
                                            'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                            'data-method' => 'post',
                                            'data-pjax' => '0',
                                        ]);
                                }
                                else {
                                    return '';
                                }
                            },
                    */
                ],
            ]
        ],
    ]); ?>

</div>
