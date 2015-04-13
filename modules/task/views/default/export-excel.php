<?php
/**
 * User: KozminVA
 * Date: 13.04.2015
 * Time: 15:12
 *
 * @var ActiveDataProvider $dataProvider
 * @var TasklistSearch searchModel
 */


use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\modules\task\models\Tasklist;

use app\components\Exportutil;

$dataProvider->prepare();
$nMaxCount = 2500;

// echo $format;
// echo ' ' . $dataProvider->pagination->pageCount;
// echo ' ' . $dataProvider->pagination->totalCount;

$objPHPExcel = new PHPExcel();
$oSheet = $objPHPExcel->getSheet(0);

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_in_memory_gzip;
// $cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_discISAM;
// $cacheSettings = array( 'dir'  => '/usr/local/tmp');

// PHPExcel_Settings::setCacheStorageMethod($cacheMethod);

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = ['memoryCacheSize'  => '8MB'];
// PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite3;
$bCache = PHPExcel_Settings::setCacheStorageMethod($cacheMethod);
// Yii::info('cache to sqlite3: ' . print_r($bCache, true));

$oDefaultStyle = $objPHPExcel->getDefaultStyle();
$oDefaultStyle->getFont()->setName('Arial');
$oDefaultStyle->getFont()->setSize(8);
/*
$objPHPExcel->getProperties()
    ->setCreator(Yii::$app->name)
    ->setLastModifiedBy(Yii::$app->name)
    ->setTitle(Yii::$app->name)
    ->setSubject("Export " . date('d.m.Y H:i:s'))
    ->setDescription("Export " . date('d.m.Y H:i:s'))
    ->setKeywords(Yii::$app->name)
    ->setCategory(Yii::$app->name);
*/

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

$nPageCount = $dataProvider->pagination->pageCount;

if( $dataProvider->pagination->totalCount > $nMaxCount ) {
    $nPageCount = floor($nMaxCount / $dataProvider->pagination->pageSize);
}

$styleTitle = array(
    'font' => array(
        'bold' => true,
        'size' => 18,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
    ),
);

$styleColTitle = array(
    'font' => array(
        'bold' => true,
        'size' => 11,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
        'wrap' => true
    ),
);

$styleSell = array(
    'font' => array(
        'bold' => false,
        'size' => 10,
    ),
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
        'wrap' => true
    ),
);



$aTitle = $searchModel->attributeLabels();
$aField = [
    [
        'w' => 16,
        'attribute' => 'task_num',
    ],
    [
        'attribute' => 'task_createtime',
        'w' => 14,
        'title' => 'Дата создания',
        'val' => function($model) { return date('d.m.Y', strtotime($model->task_createtime)); },
    ],
    [
        'w' => 30,
        'attribute' => 'task_name',
    ],
    [
        'attribute' => 'task_direct',
        'w' => 30,
    ],
    [
        'attribute' => 'task_finaltime',
         'w' => 14,
        'val' => function($model) { return date('d.m.Y', strtotime($model->task_finaltime)); },
    ],
    [
        'attribute' => 'task_actualtime',
        'w' => 14,
        'val' => function($model) { return date('d.m.Y', strtotime($model->task_actualtime)); },
    ],
    [
        'attribute' => 'task_type',
        'w' => 14,
        'title' => 'Свойство',
        'val' => function($model) { return $model->getTaskType(); },
    ],
    [
        'attribute' => 'task_progress',
        'w' => 14,
        'title' => 'Статус',
        'val' => function($model) { return $model->getTaskProgress(); },
    ],
    [
        'attribute' => 'task_numchanges',
        'w' => 14,
    ],
    [
        'attribute' => 'task_reasonchanges',
        'w' => 20,
        'val' => function($model) { return str_replace("\n", "\r\n", $model->task_reasonchanges); },
    ],
    [
        'attribute' => 'task_summary',
        'w' => 30,
        'val' => function($model) { return str_replace("\n", "\r\n", $model->task_summary); },
    ],
];

if( Yii::$app->user->can('createUser') ) {
    $aField = array_merge(
        [[
            'attribute' => 'task_dep_id',
            'w' => 30,
            'val' => function($model) { return $model->department->dep_shortname; },

        ]],
        $aField
    );

}

$nCouColumns = count($aField);
$sLastCol = chr(ord('A') + $nCouColumns - 1);
$aTit = [];
foreach($aField As $k=>$v) {
    $oSheet->getColumnDimension(chr(ord('A') + $k))->setWidth($v['w']);
    $aTit[] = isset($v['title']) ? $v['title'] : $aTitle[$v['attribute']];
}
$oSheet->fromArray(
    $aTit,
    null,
    'A4'
);
$oSheet->getStyle('A4:'.$sLastCol.'4')->applyFromArray($styleColTitle);


$oSheet->setCellValue('A1', Yii::$app->name)
    ->setCellValue('A2', 'Выгрузка от ' . date('d.m.Y H:i'));
$objPHPExcel->getActiveSheet()->mergeCells('A1:'.$sLastCol.'1');
$objPHPExcel->getActiveSheet()->mergeCells('A2:'.$sLastCol.'2');
$oSheet->getStyle('A1')->applyFromArray($styleTitle);
$oSheet->getStyle('A2')->applyFromArray($styleTitle);


$cou = 1;
$nStartRow = 5;
$nRow = $nStartRow;
$oSheet->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, $nRow-1);

for($page = 0; $page < $nPageCount; $page++) {
    Yii::info('Export page: ' . $page);
    $dataProvider->pagination->setPage($page);
    $dataProvider->refresh();

    foreach($dataProvider->getModels() As $model) {
        Yii::info('Export: ' . implode(', ', $model->attributes));
        $aData = [];
        foreach($aField As $k=>$v) {
            $val = '';
            if( isset($v['val']) && ($v['val'] instanceof Closure) ) {
                $val = call_user_func($v['val'], $model);
            }
            else {
                $sName = $v['attribute'];
                $val = $model->$sName;

            }
            $aData[] = $val;
        }

        $oSheet->fromArray(
            $aData,
            null,
            'A' . $nRow
        );
        $cou++;
        $nRow++;
    }
}

$oStyle = $oSheet->getStyle('A'.$nStartRow.':' . $sLastCol . ($nRow-1));
$oStyle->applyFromArray($styleSell);
$oStyle->getAlignment()->setWrapText(true);
$oStyle->getAlignment()->setIndent(1);

$styleBorders = [
    'borders' => [
        'allborders' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ],
        'outline' => [
            'style' => PHPExcel_Style_Border::BORDER_THIN,
            'color' => array('argb' => 'FF000000'),
        ],
    ],
];

$oSheet->getStyle('A4:' . $sLastCol . '' . ($nRow - 1))->applyFromArray($styleBorders);

$oSheet->getPageSetup()->setPrintArea('A1:' . $sLastCol . '' . ($nRow - 1));


$oUtil = new Exportutil();
$sFilename = $_SERVER['HTTP_HOST'].'-export-'.date('YmdHis').'.'.$format;
$sf = $oUtil->getFilePath($sFilename);
Yii::info('oUtil: ' . $sf);

$objWriter = null;

if( $format == 'xls' ) {
    $objWriter = new PHPExcel_Writer_Excel5($objPHPExcel);
}
else if( $format == 'xlsx' ) {
    $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
}
else if( $format == 'html' ) {
    $objWriter = new PHPExcel_Writer_HTML($objPHPExcel);
}

if( !$objWriter ) {
    throw new NotFoundHttpException('The requested page does not exist.');
}

$objWriter->save($sf);
Yii::info('oUtil1: ' . $sf);

// $objWriter->save($sf);
// Yii::$app->response->sendFile($sf);

echo $sf;
