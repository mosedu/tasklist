<?php

namespace app\modules\task\models;

use Yii;

/**
 * This is the model class for table "{{%file}}".
 *
 * @property integer $file_id
 * @property string $file_time
 * @property string $file_orig_name
 * @property integer $file_task_id
 * @property integer $file_size
 * @property string $file_type
 * @property string $file_comment
 * @property string $file_name
 */
class File extends \yii\db\ActiveRecord
{
    public $filedata;

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
            [['filedata'], 'file'],
            [['file_time'], 'safe'],
            [['file_orig_name', 'file_size', 'file_name'], 'required'],
            [['file_task_id', 'file_size'], 'integer'],
            [['file_orig_name', 'file_type', 'file_comment', 'file_name'], 'string', 'max' => 255]
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
            'file_size' => 'Размер',
            'file_type' => 'Тип',
            'file_comment' => 'Комментарий',
            'file_name' => 'Имя',
        ];
    }
}
