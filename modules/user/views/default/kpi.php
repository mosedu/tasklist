<?php

use yii\helpers\Html;
use app\modules\task\models\Tasklist;
use app\modules\user\models\User;
use app\modules\user\models\Department;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\User */

$this->title = 'Вывод KPI';

?>
<div class="user-kpi">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_dateintervalform', [
        'model' => $model,
    ]) ?>

    <?php
        //nl2br(Html::encode(print_r($data, true)));
        $nTasks = count($data);
        $bExists = ($nTasks > 0);
        $sGroup = 'task_dep_id';
        if( $bExists ) {
//            echo '<p>Задач в указанном диапазоне: ' . $nTasks . '</p>';
            echo '<div style="display: none;">'; //
            echo '<table class="table table-bordered table-striped">';
            echo '<tr><th>' . implode('</th><th>', array_keys($data[0])) . '</th><th>?</th></tr>' . "\n";
            if( isset($data[0]['worker_us_id']) ) {
                $sGroup = 'worker_us_id';
            }
        }
        $nPeriodDays = 0;
        $nTaskActiveDays = 0;
        $nOkTasks = 0;
        $nCountTasks = 0;
        $nMovedTasks = 0;
        $nAvralTasks = 0;
        $nLastTask = 0;
        $aGroup = [];
        $nLastGroup = null;
        foreach($data As $v) {
            if( !isset($aGroup[$v[$sGroup]]) ) {
                $aGroup[$v[$sGroup]] = [];
            }
            $aGroup[$v[$sGroup]][] = $v;

            if( $nLastTask == $v['task_id']) {
                continue;
            }
            $nLastTask = $v['task_id'];
            $nCountTasks += 1;
            $nPeriodDays = $v['nperioddays'];
            $nTaskActiveDays += $v['ndays'];
            $nOkTasks += $v['ok'];
            $nMovedTasks += ($v['changes'] > 0) ? 1 : 0;
            $nAvralTasks += ($v['task_type'] == Tasklist::TYPE_AVRAL) ? 1 : 0;

            $s = '<tr><td>' . implode('</td><td>', $v) . '</td><td></td></tr>';
            echo $s . "\n";
        }

        if( $bExists ) {
            echo '</table>' . "\n</div>";
            echo '<table class="table table-bordered table-striped">';
            echo '<tr><td>Количество задач в указанном диапазоне:</td><td>' . $nCountTasks . "</td></tr>\n";
            echo '<tr><td>Количество задач без нарушения базового срока:</td><td>' . sprintf("%.2f", 100 * $nOkTasks / $nCountTasks) . "% ({$nOkTasks})</td></tr>\n";
            echo '<tr><td>Количество задач с перенесением базового срока:</td><td>' . sprintf("%.2f", 100 * $nMovedTasks / $nCountTasks) . "% ({$nMovedTasks})</td></tr>\n";
            echo '<tr><td>Среднее количество задач в течение выбранного периода:</td><td>' . sprintf("%.1f", $nTaskActiveDays / $nPeriodDays) . "</td></tr>\n";
            echo '<tr><td>Количество внеплановых задач:</td><td>' . sprintf("%.2f", 100 * $nAvralTasks / $nTasks) . "% ({$nAvralTasks})</td></tr>\n";
            echo '</table>' . "\n";

            $aResult = [
//                'sGroup',
                ($sGroup == 'worker_us_id') ? 'Сотрудник' : 'Подразделение',
                'Кол-во задач',
                'В срок',
                'Задач с переносами',
                'Среднее кол-во',
                'Внеплановых задач',
            ];
            echo '<table class="table table-bordered table-striped">';
            $s = '<tr><td>' . implode('</td><td>', $aResult) . '</td><td></td></tr>';
            echo $s . "\n";
            $aGroupData = ($sGroup == 'worker_us_id') ? User::getWorkerList(0) : Department::getList(false);
            foreach($aGroup As $k=>$aGroup) {
                if( !isset($aGroupData[$k]) ) {
                    continue;
                }
                $nPeriodDays = 0;
                $nTaskActiveDays = 0;
                $nOkTasks = 0;
                $nCountTasks = 0;
                $nMovedTasks = 0;
                $nAvralTasks = 0;
                $nLastTask = 0;
                $nLastTask = 0;
                $aResult = [];
                foreach($aGroup As $v) {
                    if( $nLastTask == $v['task_id']) {
                        continue;
                    }
                    $nLastTask = $v['task_id'];
                    $nCountTasks += 1;
                    $nPeriodDays = $v['nperioddays'];
                    $nTaskActiveDays += $v['ndays'];
                    $nOkTasks += $v['ok'];
                    $nMovedTasks += ($v['changes'] > 0) ? 1 : 0;
                    $nAvralTasks += ($v['task_type'] == Tasklist::TYPE_AVRAL) ? 1 : 0;
                }
                $aResult = [
//                    $sGroup,
                    $aGroupData[$k],
                    $nCountTasks,
                    $nOkTasks,
                    $nMovedTasks,
                    sprintf("%.1f", $nTaskActiveDays / $nPeriodDays),
                    $nAvralTasks,
                ];

                $s = '<tr><td>' . implode('</td><td>', $aResult) . '</td><td></td></tr>';
                echo $s . "\n";
            }
            echo '</table>' . "\n";
        }
        else {
            echo '<p>Не найдено задач в указанном диапазоне.</p>';
        }
    ?>

</div>
