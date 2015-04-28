<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * LoginForm is the model behind the login form.
 */
class ImportXlsForm extends Model
{
    public $filename;
    public $sdest = '@webroot/upload/files';


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['filename', ], 'safe'],
            ['filename', 'file', 'maxFiles' => 1, 'maxSize' => 1000000, 'extensions' => ['xls', 'xlsx', ], ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'filename' => 'xls file',
        ];
    }

    /**
     * Process upload of file
     *
     */
    public function uploadFiles() {
        $files = UploadedFile::getInstances($this, 'filename');

//        Yii::warning('uploadFiles(): count(files) = ' . count($files));
        // if no image was uploaded abort the upload
        if( empty($files) ) {
            return '';
        }

        if( !$this->isUploadDirExists() ) {
            return '';
        }

        foreach($files As $ob) {
            /** @var  UploadedFile $ob */
            $sf = str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias($this->sdest)) . DIRECTORY_SEPARATOR . $ob->name;
//            Yii::warning('uploadFiles(): save as ' . $sf);
            $ob->saveAs($sf);
            return $sf;
        }

        return '';
    }

    /**
     * Test if upload dir axists and try to create one in not
     *
     */
    public function isUploadDirExists() {
        $sDir = Yii::getAlias($this->sdest);
        if( !is_dir($sDir) ) {
//            Yii::warning('isUploadDirExists(): ' . $sDir . ' not exists');
            $a = explode('/', $this->sdest);
            $s = '';
            while( count($a) > 0 ) {
                $s .= (($s === '') ? '' : '/') . array_shift($a);
                $sd = Yii::getAlias($s);
//                Yii::warning('isUploadDirExists(): ' . $s . ' ['.count($a).'] = ' . $sd);
                if( !is_dir($sd) && !mkdir($sd) ) {
//                    Yii::warning('isUploadDirExists(): ERROR MAKE DIR: ' . $sd);
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @return array
     */
    public function getFileData() {
        $sf = $this->uploadFiles();
        $aData = [];
        $aFields = [
            0 => 'fullname',
            1 => 'workposition',
            2 => 'department',
            3 => 'email',
            4 => 'right',
        ];

        if( ($sf != '') && file_exists($sf) ) {
            $objPHPExcel = \PHPExcel_IOFactory::load($sf);
            $oSheet = $objPHPExcel->getActiveSheet();
            $nRow = 2;
            while( true ) {
                $a = [];

                foreach($aFields As $k=>$v) {
                    $a[$v] = $oSheet->getCellByColumnAndRow($k, $nRow)->getValue();
                }

                if( $a['fullname'] == '' ) {
                    break;
                }

                $aData[] = $a;

                $nRow++;
                if( $nRow > 100 ) {
                    break;
                }
            }
        }
        else {
            $aData['error'] = 'File ' . $sf . ' not exists';
        }
        return $aData;
    }
}
