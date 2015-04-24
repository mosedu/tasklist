<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\task\models\Tasklist;
use app\modules\task\models\Action;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use yii\web\View;
use app\modules\user\models\Department;


/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\ActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Лог задач';
// $this->params['breadcrumbs'][] = $this->title;
?>
<div class="action-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'act_id',
//            'act_us_id',
//            'act_type',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'act_type',
                'filter' => Action::getAllTypes(),
                'content' => function ($model, $key, $index, $column) {
                    $a = [
                        'question-sign',
                        'plus',
                        'pencil',
                        'trash',
                    ];

                    return Html::a(
                        '<span class="inline glyphicon glyphicon-'.$a[$model->act_type].'"></span>',
                        '', // ['default/view', 'id'=>$model->task_id],
                        [
                            'title' => Html::encode($model->getTypeText($model->act_type)),
                            'data-toggle' => "tooltip",
                            'data-placement' => "top",
                            'data-html' => "false",
                            'class' => 'noanyaction',
                            'style' => "float: right; display: block; text-align: right; text-decoration: none; color: #777777;"
                        ]
                    );
//                    return '<span class="glyphicon glyphicon-'.$a[$model->act_type].'"></span><br />' . "\n<span style=\"color: #777777;\">" . $model->getTypeText($model->act_type) . '</span>'; // $model->getTypeText($model->act_type);
                },
            ],
            // 'act_createtime',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'act_createtime',
//                'filter' => false,
                'filter' => DatePicker::widget([
                    'type' => DatePicker::TYPE_INPUT,
                    'attribute' => 'act_createtime',
                    'model' => $searchModel,
                    'pluginOptions' => [
                        'format' => 'dd.mm.yyyy',
                        'todayHighlight' => true,
                        'autoclose'=>true,
                    ]
                ]),
                'content' => function ($model, $key, $index, $column) {
                    return date('d.m.Y H:i', strtotime($model->act_createtime));
                },
            ],
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'act_data',
                'filter' => Department::getList(false),
                'content' => function ($model, $key, $index, $column) {
                    $sData = empty($model->act_data) ? '' : Tasklist::getChangesLogText(unserialize($model->act_data));
                    $sTaskTitle = Html::encode($model->task->task_name);
                    return Html::a(
                            $sTaskTitle,
                            $model->task->getUrl(),
                            [
                                'class' => 'showinmodal',
                                'title' => Html::encode(mb_substr($model->task->task_name, 0, 48)),
                            ]
                        )
                         . ' ('
                         . Html::encode($model->task->department->dep_name)
                         . ') '
                         . '<br />'
                         . Html::encode($model->user->getFullName())
                         . ($sData != '' ? ('<br />' . $sData) : '') ;
                },
//                'contentOptions' => function ($model, $key, $index, $column) {
//                    $diff = date('Ymd', strtotime($model->task_finaltime)) - date('Ymd', strtotime($model->task_actualtime));
//                    return [
//                        'class' => 'griddate' . (($model->task_progress == Tasklist::PROGRESS_FINISH) ? (( $diff < 0 ) ? ' colorcell_red' : (( $diff > 0 ) ? ' colorcell_green' : '')) : ''),
//                    ];
//                },
            ],

            // 'act_data:ntext',
            // 'act_table',
            // 'act_table_pk',

//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);

    $sJs = <<<EOT
jQuery('input[name="ActionSearch[act_createtime]"]').
EOT;

    ?>

</div>
<?php
// Окно для вывода

Modal::begin([
    'header' => '<span></span>',
    'id' => 'messagedata',
    'size' => Modal::SIZE_LARGE,
]);
Modal::end();

$sJs =  <<<EOT
var params = {};
params[$('meta[name=csrf-param]').prop('content')] = $('meta[name=csrf-token]').prop('content');

jQuery('[data-toggle="tooltip"]').tooltip();

jQuery('.noanyaction').on("click", function (event){
    event.preventDefault();
    return false;
});

jQuery('.showinmodal').on("click", function (event){
    event.preventDefault();

    var ob = jQuery('#messagedata'),
        oBody = ob.find('.modal-body'),
        oLink = $(this);

    oBody.text("");
    oBody.load(oLink.attr('href'), params);
    ob.find('.modal-header span').text(oLink.attr('title'));
    ob.modal('show');
    return false;
});

EOT;
$this->registerJs($sJs, View::POS_READY, 'showmodalmessage');

?>