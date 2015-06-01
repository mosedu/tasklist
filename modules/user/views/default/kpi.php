<?php

use yii\helpers\Html;
use app\modules\task\models\Tasklist;

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
        if( $bExists ) {
//            echo '<p>Задач в указанном диапазоне: ' . $nTasks . '</p>';
            echo '<div style="display: none;">';
            echo '<table class="table table-bordered table-striped">';
            echo '<tr><th>' . implode('</th><th>', array_keys($data[0])) . '</th><th>?</th></tr>' . "\n";
        }
        $nPeriodDays = 0;
        $nTaskActiveDays = 0;
        $nOkTasks = 0;
        $nMovedTasks = 0;
        $nAvralTasks = 0;
        foreach($data As $v) {
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
            echo '<tr><td>Количество задач в указанном диапазоне:</td><td>' . $nTasks . "</td></tr>\n";
            echo '<tr><td>Количество задач без нарушения базового срока:</td><td>' . sprintf("%.2f", 100 * $nOkTasks / $nTasks) . "% ({$nOkTasks})</td></tr>\n";
            echo '<tr><td>Количество задач с перенесением базового срока:</td><td>' . sprintf("%.2f", 100 * $nMovedTasks / $nTasks) . "% ({$nMovedTasks})</td></tr>\n";
            echo '<tr><td>Среднее количество задач в течение выбранного периода:</td><td>' . sprintf("%.1f", $nTaskActiveDays / $nPeriodDays) . "</td></tr>\n";
            echo '<tr><td>Количество внеплановых задач:</td><td>' . sprintf("%.2f", 100 * $nAvralTasks / $nTasks) . "% ({$nAvralTasks})</td></tr>\n";
            echo '</table>' . "\n";
        }
        else {
            echo '<p>Не найдено задач в указанном диапазоне.</p>';
        }
    ?>

</div>
