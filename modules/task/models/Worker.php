<?php

namespace app\modules\task\models;

use Yii;

/**
 * This is the model class for table "{{%worker}}".
 *
 * @property integer $worker_id
 * @property integer $worker_task_id
 * @property integer $worker_us_id
 */
class Worker extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%worker}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_task_id', 'worker_us_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'worker_id' => 'Worker ID',
            'worker_task_id' => 'Worker Task ID',
            'worker_us_id' => 'Worker Us ID',
        ];
    }
}
