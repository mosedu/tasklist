<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\task\models\RequestmsgSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Requestmsgs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="requestmsg-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Requestmsg', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'req_id',
            'req_user_id',
            'req_task_id',
            'req_text',
            'req_comment',
            // 'req_created',
            // 'req_data:ntext',
            // 'req_is_active',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
