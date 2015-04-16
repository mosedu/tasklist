<?php

namespace app\modules\task\models;

use Yii;
use app\modules\task\models\Tasklist;

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
            'ch_us_id' => 'Пользователь',
            'ch_task_id' => 'Задача',
            'ch_data' => 'Даты',
            'ch_text' => 'Причина',
        ];
    }

    /**
     * Add new date change record
     *
     * @param Tasklist $task
     */
    public static function addChange($task) {
        $ob = new Changes;
        $aChanged = $task->getChangeattibutes();
        $sData = "{$aChanged['task_actualtime']['old']} -> {$aChanged['task_actualtime']['new']}";
        $ob->attributes = [
            'ch_us_id' => Yii::$app->user->getId(),
            'ch_task_id' => $task->task_id,
            'ch_data' => $sData,
            'ch_text' => $task->reasonchange,
        ];
        if( !$ob->save() ) {
            Yii::warning('Error save change dates: ' . print_r($ob->getErrors(), true));
        }
    }
}
