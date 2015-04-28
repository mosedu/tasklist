<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\modules\user\models\Department;
use app\modules\user\models\User;
use app\assets\GriddataAsset;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\user\models\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

GriddataAsset::register($this);

$this->title = 'Отделы';
$this->params['breadcrumbs'][] = $this->title;

// Yii::info('Grid: ' . print_r(User::getUserRoles(), true));

?>
<div class="department-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Добавить отдел', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'dep_id',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'dep_num',
                'content' => function ($model, $key, $index, $column) {
                    /** @var  Department $model */
                    return '' //'<div class="left_operate_block"><a href="#'.$model->dep_id.'_'.$model->dep_num.'" class="move_department move_up"><span class="glyphicon glyphicon-arrow-up"></span></a><a href="#'.$model->dep_id.'_'.$model->dep_num.'" class="move_department move_down"><span class="glyphicon glyphicon-arrow-down"></span></a></div>'
                         . $model->dep_num;
                }
            ],
            'dep_name',
            'dep_shortname',
//            'dep_active',
            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'dep_user_roles',
                'filter' => User::getUserRoles(),
                'content' => function ($model, $key, $index, $column) {
                    /** @var  Department $model */
                    return Html::encode(User::getRoleTitle($model->dep_user_roles));
                },
            ],

            [
                'class' => 'yii\grid\DataColumn',
                'attribute' => 'dep_active',
                'filter' => Department::getDepStatuses(),
                'content' => function ($model, $key, $index, $column) {
                    return Html::encode($model->getDepStatus());
                },
                'visible' => false,
//                'visible' => ( Yii::$app->user->can(User::ROLE_ADMIN) ) ? true : false,
/*
                'contentOptions' => function ($model, $key, $index, $column) {
                    return [
                        'class' => 'griddate' . (($model->task_progress != Tasklist::PROGRESS_FINISH) ? (( $diff < 0 ) ? ' colorcell_red' : (( $diff < 24 * 3600 * 7 ) ? ' colorcell_yellow' : '')) : ''),
                    ];
                },
*/

            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{update} {delete}',
                'contentOptions' => [
                    'class' => 'commandcell',
                ],
                'buttons'=>[
                    'view'=>function ($url, $model) {
                        return Html::a( '<span class="glyphicon glyphicon-eye-open"></span>', $url,
                            ['title' => 'Отдел ' . $model->dep_shortname, 'class'=>'showinmodal']); // , 'data-pjax' => '0'
//                            ['title' => Yii::t('yii', 'View'), 'class'=>'showinmodal']); // , 'data-pjax' => '0'
                    },
                    'update'=>function ($url, $model) {
                        return
                            Html::a( '<span class="glyphicon glyphicon-pencil"></span>', $url, ['title' => 'Изменить отдел ' . $model->dep_shortname])
                            ;
                    },
                    'delete' => function ($url, $model, $key) {
                        return $model->dep_id != 1 ?
                            Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                'title' => Yii::t('yii', 'Delete'),
                                'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                'data-method' => 'post',
                                'data-pjax' => '0',
                            ]) :
                            '';
                    }
                ],
            ],
        ],
    ]);

    $sUrl = \yii\helpers\Url::to(['changenum']);
    $sJs = <<<EOT
var oLinks = jQuery(".move_department");
oLinks.on("click", function(event){
    event.preventDefault();
    var oLink = jQuery(this),
        url = oLink.attr('href'),
        aPart = url.split("#")[1].split("_"),
        id = aPart[0],
        num = aPart[1],
        bUp = oLink.hasClass('move_up');
    jQuery.ajax({
        type: "POST",
        url: "{$sUrl}",
        data: {id: id, num: num, up: bUp ? 1 : 0},
        success: function(data, textStatus, jqXHR){
            if( ("update" in data) && (data.update > 0) ) {
                document.location.reload(true);
            }
            console.log("success: ", data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            console.log("error: ", textStatus, jqXHR);
        },
        dataType: "json"
    });
    console.log(url + " " + (bUp ? "up" : "down") + " [" + aPart.join("; ") + "]");
    return false;
});
EOT;

    $sCss = <<<EOT
.left_operate_block {
    display: block;
    float: right;
    padding: 0;
    margin: 0;
}

.left_operate_block .move_department {
    display: block;
}
EOT;
    $this->registerCss($sCss);
    $this->registerJs($sJs);
    ?>
</div>
