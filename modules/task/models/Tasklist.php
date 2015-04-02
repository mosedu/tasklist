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
 * @property integer $task_numchanges
 * @property string $task_reasonchanges
 * @property integer $task_progress
 * @property string $task_summary
 */
class Tasklist extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_TEXT_DELETED = 'Удален';
    const STATUS_TEXT_ACTIVE = 'Активен';

    const TYPE_PLAN = 0;
    const TYPE_AVRAL = 1;

    const TYPE_TEXT_PLAN = 'Плановая';
    const TYPE_TEXT_AVRAL = 'Внеплановая';

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
            [['task_dep_id', 'task_num', 'task_type', 'task_numchanges', 'task_progress'], 'integer'],
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
            'task_id' => 'id',
            'task_dep_id' => 'Отдел',
            'task_num' => 'Номер',
            'task_direct' => 'Направление',
            'task_name' => 'Наименование',
            'task_type' => 'Свойство',
            'task_createtime' => 'Создана',
            'task_finaltime' => 'Базовый срок',
            'task_actualtime' => 'Срок',
            'task_numchanges' => 'Изменения',
            'task_reasonchanges' => 'Причина',
            'task_progress' => 'Статус',
            'task_summary' => 'Отчет',
        ];
    }

    /**
     * Получение списка статусов
     * @return array список статусов - ключ - id статуса, значение - заголовок для отображения
     */
    public static function getAllStatuses()
    {
        return [
            self::STATUS_DELETED => self::STATUS_TEXT_DELETED,
            self::STATUS_ACTIVE => self::STATUS_TEXT_ACTIVE,
        ];
    }

    /**
     * Получение статуса
     * @return string
     */
    public function getTaskStatus()
    {
        $a = [
            self::STATUS_ACTIVE => self::STATUS_TEXT_ACTIVE,
            self::STATUS_DELETED => self::STATUS_TEXT_DELETED,
        ];
/*
        $a = [
            self::STATUS_ACTIVE => '+',
            self::STATUS_DELETED => '-',
        ];
*/
        return isset($a[$this->us_active]) ? $a[$this->us_active] : '~';
    }

    /**
     * Все типы задач
     *
     * @return array
     */
    public static function getAllTypes() {
        return [
            self::TYPE_PLAN => self::TYPE_TEXT_PLAN,
            self::TYPE_AVRAL => self::TYPE_TEXT_AVRAL,
        ];
    }

    /**
     * Тип задачи
     *
     * @return string
     */
    public function getTaskType() {
        $a = self::getAllTypes();
        return isset($a[$this->task_type]) ? $a[$this->task_type] : '';
    }


}
