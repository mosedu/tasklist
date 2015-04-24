<?php

use yii\helpers\Html;
use app\modules\task\models\Tasklist;
use app\modules\user\models\Department;
use app\assets\GriddataAsset;

/* @var $this yii\web\View */
/* @var $model app\modules\task\models\Tasklist */
/* @var $data array of changes */
/* @var $title array of titles */

// echo print_r($data, true);

GriddataAsset::register($this);

if( !function_exists('formatVal') ) {
    function formatVal($model, $key, $val)
    {
        if ($key == 'task_dep_id') {
            return Department::getDepartmentName($val);
        }
        else if ($key == 'task_progress') {
            return $model->getTaskProgress($val);
        }
        else if ($key == 'task_type') {
            return $model->getTaskType($val);
        }
        return $val;
    }
}

$sDop = '';
foreach($data As $k=>$v) {
    $sTitle = $title[$k];
    if( $k == 'task_actualtime' ) {
        $sTitle = "Новый срок";
    }
//    echo '<p>' . Html::encode($title[$k]) . ' ' . formatVal($k, $v['old']) . ' -> ' . formatVal($k, $v['new']) . '</p>';
    echo $sDop . Html::encode($sTitle) . ': ' . ($v['old'] !== '' ? (formatVal($model, $k, $v['old']) . ' -> ') : '') . formatVal($model, $k, $v['new']);
    $sDop = "<br />\n";
}

