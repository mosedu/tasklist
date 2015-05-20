<?php

namespace app\modules\task\models;

use Yii;
use yii\web\UploadedFile;
use yii\db\Expression;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property integer $file_id
 * @property string $file_time
 * @property string $file_orig_name
 * @property integer $file_task_id
 * @property integer $file_user_id
 * @property integer $file_group
 * @property integer $file_size
 * @property string $file_type
 * @property string $file_comment
 * @property string $file_name
 */
class File extends \yii\db\ActiveRecord
{
    const FILE_TASK_GROUP = 1;     // файл для задачи
    const FILE_SUMMARY_GROUP = 2;  // файл для отчета

    public $filedata; // загружаемый файл

    public $uploadDir = '@webroot/upload/files'; // путь к сохранению

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file_id', ], 'filter', 'filter' => 'intval'],
            [['file_id', 'file_group', ], 'integer'],
//            [['filedata'], 'required', 'message' => 'Нужно выбрать файл для загрузки', 'when' => function($model) { return $model->file_id == 0; }],
            [['filedata'], 'file', 'maxSize' => 4000000, 'extensions' => ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'doc', 'docx', 'xls', 'xlsx', 'zip', 'rar', 'tar', 'gz', 'tgz', ]],
//            [['file_time'], 'safe'],
            [['file_comment'], 'required'],
//            [['file_orig_name', 'file_size', 'file_name'], 'required'],
//            [['file_task_id', 'file_size', 'file_user_id'], 'integer'],
//            [['file_orig_name', 'file_type', 'file_comment', 'file_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file_id' => 'ID',
            'file_time' => 'Загружен',
            'file_orig_name' => 'Имя',
            'file_task_id' => 'Задача',
            'file_user_id' => 'Пользователь',
            'file_size' => 'Размер',
            'file_type' => 'Тип',
            'file_group' => 'Группа',
            'file_comment' => 'Комментарий',
            'file_name' => 'Имя',
            'filedata' => 'Файл',
        ];
    }


    /**
     *
     * Make full path to file
     *
     * @return string
     *
     */
    public function getFullpath() {
        $sDir = str_replace('/', DIRECTORY_SEPARATOR, Yii::getAlias($this->uploadDir)) . DIRECTORY_SEPARATOR . sprintf("%02x", $this->file_id % 256);
        if( !is_dir($sDir) && !@mkdir($sDir) ) {
            echo "Error Dir: {$sDir}\n";
            return null;
        }
        return $sDir . DIRECTORY_SEPARATOR . $this->file_name;
    }

    /**
     *
     * Make full path to file
     *
     * @return string
     *
     */
    public function getUrl() {
        return ['file/download', 'name' => $this->file_name];
//        $sName = $this->getFullpath();
//        return str_replace(DIRECTORY_SEPARATOR, '/', substr($sName, strlen(Yii::getAlias('@webroot'))));
    }

    /**
     * Test if upload dir axists and try to create one in not
     *
     */
    public function isUploadDirExists() {
        $sDir = Yii::getAlias($this->uploadDir);
//        Yii::info("Upload dir: {$sDir}");
        if( !is_dir($sDir) ) {
//            Yii::info("Upload dir: {$sDir} not exists");
            $a = explode('/', $this->uploadDir);
            $s = '';
            while( count($a) > 0 ) {
                $s .= (($s === '') ? '' : '/') . array_shift($a);
                $sd = Yii::getAlias($s);
//                Yii::info("Upload dir: try {$s} = {$sd}");
                if( !is_dir($sd) && !mkdir($sd) ) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $ob UploadedFile
     * @param $taskid integer Task id
     * @param $comment string Comment string
     * @param $group integer File Group
     */
    public function addFile($ob, $taskid, $comment, $group) {
        if( !$this->isUploadDirExists() ) {
            Yii::error("Error: Upload dir not exists");
            return;
        }
        if( $this->isNewRecord ) {
            $this->setDataByUpload($ob, $taskid, $comment, $group);
        }

        $this->file_comment = $comment; // . ' ' . $ob->name . ' * ' . $ob->size;

        if( $this->save() ) {
            $ob->saveAs($this->getFullpath());
        }
    }

    /**
     * @param $ob UploadedFile
     * @param $taskid integer Task id
     * @param $comment string Comment string
     * @param $group integer File Group
     */
    public function setDataByUpload($ob, $taskid, $comment, $group)
    {
        if( !$this->isNewRecord ) {
            unlink($this->getFullpath());
        }

        $a = explode(".", $ob->name);
        $ext = array_pop($a);

        $this->file_time = new Expression('NOW()');
        $this->file_orig_name = $ob->name;
        $this->file_size = $ob->size;
        $this->file_type = $ob->type;
        $this->file_name = Yii::$app->security->generateRandomString() . $ob->size . ".{$ext}";
        $this->file_user_id = Yii::$app->user->id;
        $this->file_task_id = $taskid;
        $this->file_group = $group;
        $this->file_comment = $comment;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        unlink($this->getFullpath());
        parent::delete();
    }

}
