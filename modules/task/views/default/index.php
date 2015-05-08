<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap\Modal;

use app\assets\GriddataAsset;
use app\modules\task\models\Tasklist;
use app\modules\user\models\User;

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
//            $sNumChanges = ' <b>[' . $model->task_numchanges . ']</b></a>';

            return
//                '<a href="#" data-toggle="tooltip" data-placement="top" data-html="false" title="' . $model->getTaskType() . '" style="float: right; display: block; text-align: right; text-decoration: none;"><span class="inline glyphicon glyphicon-'.$sGlyth.'" style="font-size: 1.25em;"></span></a>' .
            // '<a href="#" data-toggle="tooltip" data-placement="top" data-html="false" title="' . $model->getTaskType() . '" style="float: right; display: block; text-align: right; text-decoration: none;"></a> '
                Html::a(
                    '<span class="inline glyphicon glyphicon-'.$sGlyth.'"  style="font-size: 1.25em;"></span>',
                    '', // ['default/view', 'id'=>$model->task_id],
                    [
                        'title' => Html::encode($model->getTaskType()),
                        'data-toggle' => "tooltip",
                        'data-placement' => "top",
                        'data-html' => "false",
                        'class' => 'noanyaction',
                        'style' => "float: right; display: block; text-align: right; text-decoration: none;"
                    ]
                )
              . ' <span class="inline"><span style="font-size: 1.25em;">'
              . ($model->canEdit() ?
                    Html::a(
                        $model->getTasknum(), //department->dep_num . '.' . $model->task_num,
                        ['default/update', 'id'=>$model->task_id],
                        [
                            'title' => "Редактировать задачу: " . Html::encode($model->getTaskType()), // "Задача " . $model->getTaskType() . ', редактировать',
                        ]
                    ) :
                    $model->getTasknum()
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
        'attribute' => 'task_direct',
//                'filter' => Tasklist::getAllTypes(),
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            // $sGlyth = $model->task_type == Tasklist::TYPE_PLAN ? 'calendar' : 'flash';
            return Html::encode($model->task_direct); //  . $model->getTaskType() . ', '
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],
    ],

    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'task_name',
//                'filter' => Tasklist::getAllTypes(),
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            // $sGlyth = $model->task_type == Tasklist::TYPE_PLAN ? 'calendar' : 'flash';
//            $oUser = Yii::$app->user;
//            $bEdit = $oUser->can(User::ROLE_CONTROL) || $oUser->can(User::ROLE_DEPARTMENT);
            return
                $model->canEdit() ?
                    (
                        Html::a(
                            Html::encode($model->task_name),
                            ['view', 'id'=>$model->task_id], // update
                            [
                                'class' => 'showinmodal',
                                'title' => Html::encode($model->task_name),
                            ]
                        )
                        . (( (count($model->allworker) > 0) || $model->task_worker_id ) ?
                            ( '<span>' . Html::a(
                                    $model->task_worker_id ? Html::encode($model->worker->getFullName()) : '+',
                                    ['setworker', 'id'=>$model->task_id], // update
                                    [
                                        'class' => 'showinmodal greylink',
                                        'title' => 'Сотрудник',
                                    ]
                                )
                             . '</span>' ) : '')
                    ) :
                    (
                        Html::encode($model->task_name)
                        . ($model->task_worker_id ? ('<span>' . Html::encode($model->worker->getFullName()). '</span>') : '')
                    )
            ;
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
        'label' => "Новый срок<br />Реальный срок",
        'encodeLabel' => false,
        'filter' => false,
        'content' => function ($model, $key, $index, $column) {
            $bFinish = ($model->task_progress == Tasklist::PROGRESS_FINISH);
            $diff = date('Ymd', strtotime($model->task_finaltime)) - date('Ymd', strtotime( !$bFinish ? $model->task_actualtime : $model->task_finishtime ));

            if( ($diff == 0) && ($model->task_numchanges == 0) && (!$bFinish) ) {
                return '';
            }
            $sNumChanges = '';
            $nChanges = count($model->changes);
//            if( $nChanges > 0 ) {
            if( $model->task_numchanges > 0 ) {
                $sTip = array_reduce(
                    // explode("\n", $model->task_reasonchanges),
                    $model->changes,
                    function($carry, $item) {
                        // $item = trim($item);
                        // if( $item !== '' ) {
                            // $a = explode("\t", $item);
                            // $carry .= (($carry !== '') ? "<br />\n" : "") . Html::encode($a[0]) . ' ' . str_replace('\\n', ' ', Html::encode($a[1]));
                            // $carry .= (($carry !== '') ? "<br />" : "") . Html::encode($item->ch_data) . ' ' . str_replace(["\r", "\n"], ['', ' '], Html::encode($item->ch_text));
                            $carry .= (($carry !== '') ? "<br />" : "") . Html::encode($item->ch_data);
                        // }
                        return $carry;
                    },
                    ''
                );
                $sTip .= "<br />\n<span style='text-align: left;'>" . str_replace(["\r", "\n"], ['', "<br />\n"], Html::encode($model->task_reasonchanges)) . "</span>";
                $nChanges = $model->task_numchanges;
                $sNumChanges = ' <a href="#" data-toggle="tooltip" data-placement="top" data-html="true" title="'.$sTip.'"><b>[' . $nChanges . ']</b></a>';
            }

            return date('d.m.Y', strtotime(!$bFinish ? $model->task_actualtime : $model->task_finishtime)) . $sNumChanges;
        },
        'contentOptions' => function ($model, $key, $index, $column) {
            $bFinish = ($model->task_progress == Tasklist::PROGRESS_FINISH);
            $diff = date('Ymd', strtotime($model->task_finaltime)) - date('Ymd', strtotime(!$bFinish ? $model->task_actualtime : $model->task_finishtime));
            return [
                'class' => 'griddate' . ($bFinish ? (( $diff < 0 ) ? ' colorcell_red' : (( $diff > 0 ) ? ' colorcell_green' : '')) : ''),
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
            'class' => 'griddate commandcell',
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
                return nl2br(Html::encode($model->task_summary));
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
        'buttons'=>[
            'update'=>function ($url, $model) {
                return $model->canEdit() ? Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Изменить']) : '';
            },
        ],

        ]]
);

$aStat = Tasklist::getStatdata(empty($searchModel->task_dep_id) ? null : $searchModel->task_dep_id);
// $sDop = print_r($aStat, true);

$sDop = 'Задачи: активные: ' . $aStat['active']
      . ', просроченные: ' . $aStat['defect']
      . ', отложенные: ' . $aStat['wait'] . '.';

$oUser = Yii::$app->user;
// $bEdit = $oUser->can(User::ROLE_CONTROL) || $oUser->can(User::ROLE_DEPARTMENT);
$bCreateTask = Yii::$app->user->can('createTask');

?>

<div>

    <div class="col-sm-12">
        <h1><?= '<span id="id_empty_search" class="glyphicon glyphicon-search" style="font-size: 0.7em; color: #ff8888; '.((!$searchModel->showFilterForm && !$searchModel->isEmpty()) ? '' : 'display: none;').'"></span> ' . Html::encode($this->title) ?></h1>
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
            <?= $bCreateTask ? Html::a('Добавить задачу', ['create'], ['class' => 'btn btn-success']) : '' ?>
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

$sJs .=  <<<EOT

jQuery('[data-toggle="tooltip"]').tooltip();

EOT;

$this->registerJs($sJs, View::POS_READY , 'togglesearchpanel');

if( !isset(Yii::$app->params['panelcheckbox']) ) {
    Yii::$app->params['panelcheckbox'] = [];
}
Yii::$app->params['panelcheckbox'] = array_merge(
    Yii::$app->params['panelcheckbox'],
    [
        'exporttoexcelbutton' => [
            'icon' => 'floppy-disk',
            'link' => Url::to(array_merge(['export'], $searchModel->getSearchParams(), ['format' => 'xls'])),
            'linkOptions' => ['target' => '_blank'],
            'title' => Html::encode('Экспорт в Excel'),
        ],
    ]
);

?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => null, // $searchModel,
        'headerRowOptions' => ['class' => 'center-top'],
        'columns' => $aColumns,
    ]); ?>

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

$sCss =  <<<EOT
.table > thead > tr.center-top > th {
    text-align: center;
    vertical-align: middle;
}
EOT;

$this->registerJs($sJs, View::POS_READY, 'showmodalmessage');
$this->registerCss($sCss);
?>