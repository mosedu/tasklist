<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\web\View;

use app\modules\user\models\User;
use app\modules\user\models\Department;
use app\assets\GriddataAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

GriddataAsset::register($this);

$this->title = 'Сотрудники';
$this->params['breadcrumbs'][] = $this->title;
$aColumns = [];

if( Yii::$app->user->can('createUser') ) {
    $aColumns[] = [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'us_dep_id',
        'filter' => Department::getList(false),
        'content' => function ($model, $key, $index, $column) {
            /** @var User $model */
            return ($model->department !== null) ? Html::encode($model->department->dep_shortname) : '';
        },
    ];
}

$aColumns = array_merge($aColumns, [
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'fname',
        'content' => function ($model, $key, $index, $column) {
            /** @var User $model */
            $ar = User::getAllRoles();
            return Html::encode($model->getFullName()) . '<span>' . Html::encode($ar[$model->us_role_name] . ', ' . $model->us_workposition) . '</span>';
        },
        'contentOptions' => [
            'class' => 'griddate',
        ],

    ],

    'us_email:email',
    /** @var User $model */
/*
    [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'us_role_name',
        'filter' => User::getUserRoles(),
        'content' => function ($model, $key, $index, $column) {
            return Html::encode(User::getRoleTitle($model->us_role_name));
        },
    ],
*/
]);

if( Yii::$app->user->can('admin') ) {
    $aColumns[] = [
        'class' => 'yii\grid\DataColumn',
        'attribute' => 'us_active',
        'filter' => User::getUserStatuses(),
        'content' => function ($model, $key, $index, $column) {
            return Html::encode($model->getUserStatus());
        },
    ];
}

$aColumns[] = [
    'class' => 'yii\grid\ActionColumn',
    'contentOptions' => [
        'class' => 'commandcell',
    ],
    'template' => '{view} {update}', //  {delete}
    'buttons'=>[
        'view'=>function ($url, $model) {
            return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', $url,
                ['title' => Html::encode($model->getFullName()), 'class'=>'showinmodal']); // , 'data-pjax' => '0'
        },
        'update'=>function ($url, $model) {
            /** @var User $model */
            return in_array($model->us_role_name, array_keys(User::getWorkerRoles())) ? Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Изменить']) : '';
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
    ],
];

?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить сотрудника', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => $aColumns,
    ]);


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