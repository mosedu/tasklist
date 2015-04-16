<?php

namespace app\modules\task\models;

use Yii;

/**
 * This is the model class for table "{{%changes}}".
 *
 * @property integer $ch_id
 * @property integer $ch_us_id
 * @property integer $ch_task_id
 * @property string $ch_data
 * @property string $ch_text
 */
class Changes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%changes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ch_us_id', 'ch_task_id'], 'integer'],
            [['ch_text'], 'string'],
            [['ch_data'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'ch_id' => 'Ch ID',
            'ch_us_id' => 'Ch Us ID',
            'ch_task_id' => 'Ch Task ID',
            'ch_data' => 'Ch Data',
            'ch_text' => 'Ch Text',
        ];
    }
}
