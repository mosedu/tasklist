<?php

namespace app\modules\task\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "{{%taskflag}}".
 *
 * @property integer $tf_id
 * @property integer $tf_flag
 * @property integer $tf_task_id
 * @property string $tf_date
 */
class Taskflag extends \yii\db\ActiveRecord
{
    const FLAG_SEND_3_DAY_EMAIL = 1;
    const FLAG_SEND_1_DAY_EMAIL = 2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%taskflag}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tf_flag', 'tf_task_id'], 'integer'],
            [['tf_date'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'tf_id' => 'Tf ID',
            'tf_flag' => 'Флаг',
            'tf_task_id' => 'Задача',
            'tf_date' => 'Установлен',
        ];
    }

    /**
     * Установка флага для задачи
     *
     * @param int $taskId
     * @param int $flag
     */
    public static function setTaskFlags($taskId = 0, $flag = 0) {
        $sSql = 'Update ' . self::tableName() . ' Set tf_flag = :flag, tf_task_id = :task, tf_date = NOW() Where tf_task_id = 0 Limit 1';
        $nUpdate = Yii::$app
            ->db
            ->createCommand($sSql, [':flag' => $flag, ':task' => $taskId])
            ->execute();
        if( $nUpdate == 0 ) {
            $ob = new Taskflag();
            $ob->attributes = [
                'tf_flag' => $flag,
                'tf_task_id' => $taskId,
                'tf_date' => new Expression('NOW()'),
            ];
            if( !$ob->save() ) {
                Yii::info('Error save taskflag: ' . print_r($ob->getErrors(), true));
            }
        }
    }

}
