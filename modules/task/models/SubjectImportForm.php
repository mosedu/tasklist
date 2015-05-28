<?php

namespace app\modules\task\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use PHPExcel_IOFactory;

/**
 * ContactForm is the model behind the contact form.
 */
class SubjectImportForm extends Model
{
    public $xlsfile;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
//            [['xlsfile', ], 'required', ],
            [['xlsfile', ], 'file', 'maxSize' => 1000000, 'extensions' => ['xls', 'xlsx', ], ],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'xlsfile' => 'Файл данных',
        ];
    }

    /**
     * @return array Subjct data
     */
    public function getFileData()
    {
        $aData = [];
        $ofile = UploadedFile::getInstance($this, 'xlsfile');
        if( $ofile === null ) {
            return $aData;
        }
        Yii::info('ofile = ' . print_r($ofile, true));

        $inputFileName = $ofile->tempName;

        /** Load $inputFileName to a PHPExcel Object  **/
        $objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
        $objWorksheet = $objPHPExcel->getActiveSheet();
        $bFind = false; // индикатор найденности заполненных ячеек
        $nFinishRow = 10;
        for($col = 0; $col < 20; $col++) {
//            Yii::info('getFileData() col = ' . $col);
            if( $bFind ) {
                break;
            }
            for($row = 0; $row < $nFinishRow; $row++) {
                $sVal = $objWorksheet->getCellByColumnAndRow($col, $row)->getValue();
//                Yii::info('getFileData() row = ' . $row . ' val = ' . $sVal . ' bFind = ' . ($bFind ? 'true' : 'false'));
                if( $sVal == '' ) {
                    if( $bFind ) { // если пустая ячека встретилась после заполненных
                        break;
                    }
                    continue;
                }
                $bFind = true;
                $aData[] = $sVal;
                $nFinishRow++; // чтобы не закончился цикл раньше срока
            }
        }
        return $aData;
    }
}
