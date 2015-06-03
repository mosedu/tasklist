<?php

use yii\helpers\Html;
use app\modules\task\models\Tasklist;
use app\modules\user\models\User;
use app\modules\user\models\Department;
use app\components\Exportutil;
use app\modules\user\models\DateIntervalForm;

/* @var $this yii\web\View */
/* @var $model app\modules\user\models\DateIntervalForm */

$this->title = 'Вывод KPI';
$bExcel = true;
if( $bExcel ) {
    $objPHPExcel = new PHPExcel();
    $oSheet = $objPHPExcel->getSheet(0);
    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
    $cacheSettings = ['memoryCacheSize'  => '8MB'];

    $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
    $bCache = PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

    $oDefaultStyle = $objPHPExcel->getDefaultStyle();
    $oDefaultStyle->getFont()->setName('Arial');
    $oDefaultStyle->getFont()->setSize(8);

    $oSheet->getPageSetup()
        ->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE)
        ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4)
        ->setFitToPage(true)
        ->setFitToWidth(1)
        ->setFitToHeight(0);

    $oSheet->getPageMargins()
        ->setTop(0.5)
        ->setRight(0.35)
        ->setLeft(0.35)
        ->setBottom(1);

    $oSheet->getHeaderFooter()
        ->setEvenFooter('&CСтраница &P [&N]')
        ->setOddFooter('&CСтраница &P [&N]');


    $styleTitle = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
        ),
    );

    $styleLeftTitle = array(
        'font' => array(
            'bold' => true,
            'size' => 10,
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
        ),
    );

    $styleCell = array(
        'font' => array(
            'bold' => false,
            'size' => 10,
        ),
//        'alignment' => array(
//            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
//            'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
//            'wrap' => true
//        ),
    );

    $styleRightCell = array(
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
        ),
    );
}

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;

if( !function_exists('calcKpi') ) {
    function calcKpi(&$data)
    {
        $nPeriodDays = 0;
        $nTaskActiveDays = 0;
        $nOkTasks = 0;
        $nCountTasks = 0;
        $nMovedTasks = 0;
        $nAvralTasks = 0;
        $nLastTask = 0;
        $aGroup = [];
        $nLastGroup = null;
        foreach ($data As $v) {
            if ($nLastTask == $v['task_id']) {
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

        return [
            'taskcount' => $nCountTasks,
            'oktaskcount' => $nOkTasks,
            'movetaskcount' => $nMovedTasks,
            'avraltaskcount' => $nAvralTasks,
            'taskdayscount' => $nTaskActiveDays,
            'dayscount' => $nPeriodDays,
        ];
    }
}
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
            if( isset($data[0]['worker_us_id']) ) {
                $sGroup = 'worker_us_id';
            }
            $aRes = calcKpi($data);
            if( $bExcel ) {
                if( !empty($model->department_id) ) {
                    $a = Department::getList(false);
                    $sDep = $a[$model->department_id];
                }
                else {
                    $sDep = 'ГАО ТемоЦентр';
                }
                $s = $sDep . ' KPI за период ' . $model->from_date . ' - ' . $model->to_date;
                $oSheet->setCellValue('A1', $s)
                    ->setCellValue('A2', 'Выгрузка от ' . date('d.m.Y H:i'));
                $oSheet->mergeCells('A1:K1');
                $oSheet->mergeCells('A2:K2');
                $oSheet->getStyle('A1:K2')->applyFromArray($styleTitle);



                $oSheet->setCellValueByColumnAndRow(1, 4, 'Количество задач в указанном диапазоне');
                $oSheet->setCellValueByColumnAndRow(1, 5, 'Количество задач без нарушения базового срока');
                $oSheet->setCellValueByColumnAndRow(1, 6, 'Количество задач с перенесением базового срока');
                $oSheet->setCellValueByColumnAndRow(1, 7, 'Среднее количество задач в течение выбранного периода');
                $oSheet->setCellValueByColumnAndRow(1, 8, 'Количество внеплановых задач');

                $oSheet->setCellValueByColumnAndRow(2, 4, $aRes['taskcount']);
                $oSheet->setCellValueByColumnAndRow(2, 5, sprintf("%.2f", 100 * $aRes['oktaskcount'] / $aRes['taskcount']) . '%');
                $oSheet->setCellValueByColumnAndRow(2, 6, sprintf("%.2f", 100 * $aRes['movetaskcount'] / $aRes['taskcount']) . '%');
                $oSheet->setCellValueByColumnAndRow(2, 7, sprintf("%.1f", $aRes['taskdayscount'] / $aRes['dayscount']));
                $oSheet->setCellValueByColumnAndRow(2, 8, sprintf("%.2f", 100 * $aRes['avraltaskcount'] / $aRes['taskcount']) . '%');

//                $oSheet->setCellValueByColumnAndRow(3, 5, $aRes['oktaskcount']);
//                $oSheet->setCellValueByColumnAndRow(3, 6, $aRes['movetaskcount']);
//                $oSheet->setCellValueByColumnAndRow(3, 7, $aRes['taskdayscount']);
//                $oSheet->setCellValueByColumnAndRow(3, 8, $aRes['avraltaskcount']);

                $oSheet->getStyle('B4:B8')->applyFromArray($styleLeftTitle);
                $oSheet->getColumnDimension('B')->setWidth(67);
                $oSheet->getStyle('C4:D8')->applyFromArray($styleCell);
                $oSheet->getStyle('C4:D8')->applyFromArray($styleRightCell);
            }
            echo '<table class="table table-bordered table-striped">';
            echo '<tr><td>Количество задач в указанном диапазоне:</td><td>' . $aRes['taskcount'] . "</td></tr>\n";
            echo '<tr><td>Количество задач без нарушения базового срока:</td><td>' . sprintf("%.2f", 100 * $aRes['oktaskcount'] / $aRes['taskcount']) . "% ({$aRes['oktaskcount']})</td></tr>\n";
            echo '<tr><td>Количество задач с перенесением базового срока:</td><td>' . sprintf("%.2f", 100 * $aRes['movetaskcount'] / $aRes['taskcount']) . "% ({$aRes['movetaskcount']})</td></tr>\n";
            echo '<tr><td>Среднее количество задач в течение выбранного периода:</td><td>' . sprintf("%.1f", $aRes['taskdayscount'] / $aRes['dayscount']) . "</td></tr>\n";
            echo '<tr><td>Количество внеплановых задач:</td><td>' . sprintf("%.2f", 100 * $aRes['avraltaskcount'] / $aRes['taskcount']) . "% ({$aRes['avraltaskcount']})</td></tr>\n";
            echo '</table>' . "\n";

            $aGroup = [];
            foreach($data As $v) {
                if( empty($v[$sGroup]) ) {
                    continue;
                }
                if( !isset($aGroup[$v[$sGroup]]) ) {
                    $aGroup[$v[$sGroup]] = [];
                }
                $aGroup[$v[$sGroup]][] = $v;
            }

            $bGroupExists = (count($aGroup) > 0);
            $aResult = [
                ($sGroup == 'worker_us_id') ? 'Сотрудник' : 'Подразделение',
                'Кол-во задач',
                'В срок',
                'Задач с переносами',
                'Среднее кол-во',
                'Внеплановых задач',
            ];


            if($bGroupExists) {
                echo '<table class="table table-bordered table-striped">';
                $s = '<tr><td>' . implode('</td><td>', $aResult) . '</td><td></td></tr>';
                echo $s . "\n";
                if ($bExcel) {
                    $nRow = 4;
                    $oSheet->fromArray(
                        $aResult,
                        null,
                        'F' . $nRow
                    );
                    $oSheet->getStyle('F' . $nRow . ':K' . $nRow)->applyFromArray($styleTitle);
                    $oSheet->getColumnDimension('F')->setWidth(30);
                    $oSheet->getColumnDimension('G')->setWidth(24);
                    $oSheet->getColumnDimension('H')->setWidth(24);
                    $oSheet->getColumnDimension('I')->setWidth(24);
                    $oSheet->getColumnDimension('J')->setWidth(24);
                    $oSheet->getColumnDimension('K')->setWidth(24);
                }
                $aGroupData = ($sGroup == 'worker_us_id') ? User::getWorkerList($model->department_id) : Department::getList(false);
                foreach ($aGroup As $k => $aGroup) {
                    if (!isset($aGroupData[$k])) {
                        continue;
                    }
                    $aRes = calcKpi($aGroup);
                    $aResult = [
//                    $sGroup,
                        $aGroupData[$k],
                        $aRes['taskcount'], // $nCountTasks,
                        sprintf("%.2f", 100 * $aRes['oktaskcount'] / $aRes['taskcount']) . '% (' . $aRes['oktaskcount'] . ')', // $nOkTasks,
                        sprintf("%.2f", 100 * $aRes['movetaskcount'] / $aRes['taskcount']) . '% (' . $aRes['movetaskcount'] . ')', // $nMovedTasks,
                        sprintf("%.1f", $aRes['taskdayscount'] / $aRes['dayscount']), // $nTaskActiveDays / $nPeriodDays),
                        sprintf("%.2f", 100 * $aRes['avraltaskcount'] / $aRes['taskcount']) . '% (' . $aRes['avraltaskcount'] . ')', // $nAvralTasks,
                    ];
                    $s = '<tr><td>' . implode('</td><td>', $aResult) . '</td><td></td></tr>';
                    echo $s . "\n";
                    if ($bExcel) {
                        $nRow += 1;
                        $oSheet->fromArray(
                            $aResult,
                            null,
                            'F' . $nRow
                        );
                        $oSheet->getStyle('F' . $nRow . ':K' . $nRow)->applyFromArray($styleCell);
                        $oSheet->getStyle('G' . $nRow . ':K' . $nRow)->applyFromArray($styleRightCell);
                    }

                }
                echo '</table>' . "\n";
            }

            if( $bExcel ) {
                $format = 'xls';
                $oUtil = new Exportutil();
                $sFilename = $_SERVER['HTTP_HOST'] . '-export-' . date('YmdHis') . '.' . $format;
                $oUtil->deleteOldFiles();
                $sf = $oUtil->getFilePath($sFilename);
                $objWriter = null;

                if( $format == 'xls' ) {
                    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
                }

                if( $objWriter !== null ) {
                    $objWriter->save($sf);
                    $s = substr($sf, strlen($_SERVER['DOCUMENT_ROOT']));
                    $s = str_replace(DIRECTORY_SEPARATOR, '/', $s);
                    echo Html::tag('p', Html::a('Загрузить ' . $format, $s, ['target' => '_blank']));
                }

            }


        }
        else {
            echo '<p>Не найдено задач в указанном диапазоне.</p>';
        }
    ?>

</div>
