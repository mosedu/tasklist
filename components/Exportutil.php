<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 08.04.2015
 * Time: 16:50
 */

namespace app\components;

use \Yii;
use yii\base\NotSupportedException;

class Exportutil {

    /**
     * Получение папки для сохранения
     * @return mixed
     * @throws NotSupportedException
     */
    public function getExportDir() {
        $sf = Yii::getAlias('@webroot/upload/export');

        if( !is_dir($sf) && !@mkdir($sf) ) {
            throw new NotSupportedException('Not exists directory: ' . basename($sf));
        }

        return $sf;
    }

    /**
     * Удаление старых файлов
     * @throws NotSupportedException
     */
    public function deleteOldFiles() {
        $aFiles = glob($this->getExportDir() . DIRECTORY_SEPARATOR . '*');
        $tOld = time() - 3600;
        foreach($aFiles As $v) {
            if( filemtime($v) < $tOld ) {
                unlink($v);
            }
        }
    }

    /**
     * Получение полного пути по файла
     * @param $name
     * @return string
     * @throws NotSupportedException
     */
    public function getFilePath($name) {
        $this->deleteOldFiles();
        return $this->getExportDir() . DIRECTORY_SEPARATOR . $name;
    }
}