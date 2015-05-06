<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = $model->us_id;
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'us_email:email',
            [
                'label' => 'ФИО',
                'value' => $model->getFullName(),
            ],
            [
                'label' => 'Отдел',
                'value' => $model->department->dep_name . ' (' . $model->department->dep_shortname . ')',
            ],

            'us_workposition',
        ],
    ]) ?>

</div>
