<?php

namespace app\modules\task\models;

use Yii;

/**
 * This is the model class for table "{{%tasklist}}".
 *
 * @property integer $task_id
 * @property integer $task_dep_id
 * @property integer $task_num
 * @property string $task_direct
 * @property string $task_name
 * @property integer $task_type
 * @property string $task_createtime
 * @property string $task_finaltime
 * @property string $task_actualtime
 * @property integer $task_timechanges
 * @property string $task_reasonchanges
 * @property integer $task_progress
 * @property string $task_summary
 */
class Tasklist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasklist}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_dep_id', 'task_num', 'task_type', 'task_timechanges', 'task_progress'], 'integer'],
            [['task_direct', 'task_name', 'task_reasonchanges', 'task_summary'], 'string'],
            [['task_name', 'task_createtime', 'task_finaltime', 'task_actualtime'], 'required'],
            [['task_createtime', 'task_finaltime', 'task_actualtime'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'task_id' => 'Task ID',
            'task_dep_id' => 'Task Dep ID',
            'task_num' => 'Task Num',
            'task_direct' => 'Task Direct',
            'task_name' => 'Task Name',
            'task_type' => 'Task Type',
            'task_createtime' => 'Task Createtime',
            'task_finaltime' => 'Task Finaltime',
            'task_actualtime' => 'Task Actualtime',
            'task_timechanges' => 'Task Timechanges',
            'task_reasonchanges' => 'Task Reasonchanges',
            'task_progress' => 'Task Progress',
            'task_summary' => 'Task Summary',
        ];
    }
}
