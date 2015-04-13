<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;

use app\assets\GriddataAsset;
use app\modules\task\models\Tasklist;

GriddataAsset::register($this);

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\TasklistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = (Yii::$app->user->identity->department !== null) ? Yii::$app->user->identity->department->dep_name : 'Задачи';
$this->params['breadcrumbs'][] = $this->title;

$aColumns = [
//            ['class' => 'yii\grid\SerialColumn'],
//            'task_id',
//            'task_dep_id',
    // 'task_num',
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_num',
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            $sGlyth = $model->task_type == Tasklist::TYPE_PLAN ? 'calendar' : 'flash';
            $sColor = '#999999';
            $nt1 = strtotime($model->task_finaltime);
            $ntcur = time();
            if( $nt1 < $ntcur ) {
                $sColor = '#ff9999';
                if( strtotime($model->task_actualtime) < $ntcur ) {
                    $sColor = '#ff0000';
                }
            }
//                        color: '.$model->.';
            // <span class="inline glyphicon glyphicon-'.$sGlyth.'" style=" color: ' . $sColor . '; font-size: 1.25em;">
            return
                '<span class="inline glyphicon glyphicon-'.$sGlyth.'" style="float: right; display: block; font-size: 1.25em; text-align: right;"><br />' . $model->getTaskType() . '</span>' .
                '<span class="inline"><span style="font-size: 1.25em;"> ' . Html::a(
                $model->task_num,
                ['default/update', 'id'=>$model->task_id],
                ['title' => "Задача " . $model->getTaskType() . ', редактировать']
            ) . '</span></span>';
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],
//            'task_direct:ntext',
//            'task_name:ntext',
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_name',
//                'filter' => Tasklist::getAllTypes(),
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            // $sGlyth = $model->task_type == Tasklist::TYPE_PLAN ? 'calendar' : 'flash';
            return Html::a(Html::encode($model->task_name), ['update', 'id'=>$model->task_id]) . '<span>' . $model->task_direct . '</span>'; //  . $model->getTaskType() . ', '
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],
    // 'task_type',
    /*            [
                    'class' => 'yii\grid\DataColumn',
                    // 'header' => 'Состояние',
                    'attribute' => 'task_type',
                    'filter' => Tasklist::getAllTypes(),
                    // 'filterOptions' => ['class' => 'gridwidth7'],
                    'content' => function ($model, $key, $index, $column) {
                        return Html::encode($model->getTaskType());
    //                    return '<span class="glyphicon glyphicon-'.$model->flag->fl_glyth.'" style="color: '.$model->flag->fl_glyth_color.'; font-size: 1.25em;"></span>' //  font-size: 1.25em;
    //                    . '<span class="inline">' . $model->flag->fl_sname . '</span>';
                    },
                    'contentOptions' => [
                        'class' => 'griddate',
                    ],
                ],
    */
    // 'task_createtime',
    // 'task_finaltime',
    // 'task_actualtime',
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_finaltime',
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            /*                    $sdop = '';
                                if( $model->task_actualtime != $model->task_finaltime ) {
                                    $sdop .= '<span>' . date('d.m.Y', strtotime($model->task_finaltime)) . (($model->task_numchanges > 0) ? (' <b>[' . $model->task_numchanges . ']</b>') : '') . '</span>';
                                }
            */
            return date('d.m.Y', strtotime($model->task_finaltime)); // . $sdop;
        },
        'contentOptions' => function ($model, $key, $index, $column) {
            $diff = strtotime($model->task_finaltime) - time();

            return [
                'class' => 'griddate' . (($model->task_progress != Tasklist::PROGRESS_FINISH) ? (( $diff < 0 ) ? ' colorcell_red' : (( $diff < 24 * 3600 * 7 ) ? ' colorcell_yellow' : '')) : ''),
            ];
        },
    ],
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_actualtime',
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            // $diff = date('Ymd', strtotime($model->task_finaltime)) - date('Ymd', strtotime($model->task_actualtime));
            return date('d.m.Y', strtotime($model->task_actualtime)) . (($model->task_numchanges > 0) ? (' <b>[' . $model->task_numchanges . ']</b>') : '');
        },
        'contentOptions' => function ($model, $key, $index, $column) {
            $diff = date('Ymd', strtotime($model->task_finaltime)) - date('Ymd', strtotime($model->task_actualtime));
            return [
                'class' => 'griddate' . (($model->task_progress == Tasklist::PROGRESS_FINISH) ? (( $diff < 0 ) ? ' colorcell_red' : (( $diff > 0 ) ? ' colorcell_green' : '')) : ''),
            ];
        },
    ],
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_progress',
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            return $model->getTaskProgress();
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],
    // 'task_numchanges',
    // 'task_reasonchanges:ntext',
    // 'task_progress',
    // 'task_summary:ntext',

];

if( Yii::$app->user->can('createUser') ) {
    $aColumns = array_merge(
        [[
            'class' => 'yii\grid\DataColumn',
            'attribute' => 'task_dep_id',
            'filter' => false,
            'content' => function ($model, $key, $index, $column) {
                return Html::encode($model->department->dep_shortname);
            },
            'contentOptions' => [
                'class' => 'griddate',
            ],
        ]],
        $aColumns
    );
}

if( $searchModel->showTaskSummary ) {
    $aColumns = array_merge(
        $aColumns,
        [[
            'class' => 'yii\grid\DataColumn',
            'attribute' => 'task_summary',
            'filter' => false,
            'content' => function ($model, $key, $index, $column) {
                return Html::encode($model->task_summary);
            },
            'contentOptions' => [
                'class' => 'griddate',
            ],
        ]]
    );

}

$aColumns = array_merge(
    $aColumns,
    [[

    'class' => 'yii\grid\ActionColumn',
    'template'=>'{update}' . (Yii::$app->user->can('createUser') ? ' {delete}' : ''), // {view}  {answer} {toword}
    'contentOptions' => [
        'class' => 'commandcell',
    ],
    ]]
);


$aStat = Tasklist::getStatdata();
// $sDop = print_r($aStat, true);

$sDop = 'Задачи: активные: ' . $aStat['active']
      . ' просроченные: ' . $aStat['defect']
      . ' отложенные: ' . $aStat['wait'];

?>

<div>

    <div class="col-sm-12">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <?php echo ''; /* $this->render('_search', ['model' => $searchModel]); */ ?>
    <div class="col-sm-8 no-horisontal-padding">
        <div class="form-group">
            <p><span style="color: #999999;"><?= date('d.m.Y') ?></span> <?= Html::encode($sDop) ?></p>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-sm-2 no-horisontal-padding">
        <div class="form-group">
            <?= '' /*Html::a('Скрыть', '#', ['class' => 'btn btn-default pull-right no-horisontal-margin', 'id'=>'hidesearchpanel', 'role'=>"button"])*/ ?>
            <div class="clearfix"></div>
        </div>
    </div>
    <div class="col-sm-2 no-horisontal-padding">
        <div class="form-group">
            <?= Html::a('Добавить задачу', ['create'], ['class' => 'btn btn-success']) ?>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<div class="clearfix"></div>

<div class="tasklist-index">

<?php
$idserchblock = 'idsearchpanel';
echo $this->render(
    '_search',
    [
        'model' => $searchModel,
        'action' => ['index'],
        'idserchblock' => $idserchblock,
    ]
);

// показ/скрытие формы фильтрации
$sJs =  <<<EOT
var oPanel = jQuery("#{$idserchblock}"),
    oLink = jQuery("#hidesearchpanel"),
    renameButton = function() {
        oLink.text((oPanel.is(":visible") ? "Скрыть" : "Показать") + " форму поиска");
    },
    toggleSearchPanel = function() {
        oPanel.toggle();
        renameButton();
    };

renameButton();
oLink.on(
    "click",
    function(event){ event.preventDefault(); toggleSearchPanel(); return false; }
);

EOT;
$this->registerJs($sJs, View::POS_READY , 'togglesearchpanel');

?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $aColumns,
    ]); ?>

</div>
