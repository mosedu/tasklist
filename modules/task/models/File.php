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
            'file_id' => 'File ID',
            'file_time' => 'File Time',
            'file_orig_name' => 'File Orig Name',
            'file_task_id' => 'File Task ID',
            'file_size' => 'File Size',
            'file_type' => 'File Type',
            'file_comment' => 'File Comment',
            'file_name' => 'File Name',
        ];
    }
}
