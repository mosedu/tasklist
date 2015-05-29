<?php

use yii\helpers\Html;


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
        $bExists = (count($data) > 0);
        if( $bExists ) {
            echo '<table  class="table table-bordered table-striped">';
            echo '<tr><th>' . implode('</th><th>', array_keys($data[0])) . '</th><th>?</th></tr>' . "\n";
        }
        foreach($data As $v) {
//            if( $v[] )
            $s = '<tr><td>' . implode('</td><td>', $v) . '</td><td></td></tr>';
            echo $s . "\n";
        }
        if( $bExists ) {
            echo '</table>' . "\n";
        }
    ?>

</div>
