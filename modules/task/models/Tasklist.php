<?php

namespace app\modules\task\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

use app\components\AttributewalkBehavior;
use app\modules\user\models\Department;

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
 * @property integer $task_active
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

    const PROGRESS_STOP = 0;
    const PROGRESS_WORK = 1;
    const PROGRESS_FINISH = 2;
    const PROGRESS_WAIT = 3;

    const PROGRESS_TEXT_STOP = 'Не начата';
    const PROGRESS_TEXT_WORK = 'Выполняется';
    const PROGRESS_TEXT_FINISH = 'Завершена';
    const PROGRESS_TEXT_WAIT = 'Отложена';


    public function behaviors()
    {
        return [
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['task_dep_id', 'task_actualtime', 'task_finaltime', 'task_active', 'task_createtime', 'task_num', ],
                ],
                'value' => function ($event, $attribute) {
                    /** @var Tasklist $model */
                    $model = $event->sender;
                    switch($attribute) {
                        case 'task_actualtime':
                            return date('Y-m-d H:i:s', strtotime($model->task_actualtime) + 24 * 3600 - 1);

                        case 'task_finaltime':
                            return $model->task_actualtime; // дата установленная при создании - это наша плановая

                        case 'task_dep_id':
                            $model->setDepartmentByUser();
                            return $model->task_dep_id;

                        case 'task_createtime':
                            return new Expression('NOW()');

                        case 'task_active':
                            return self::STATUS_ACTIVE;

                        case 'task_num':
                            return Tasklist::getCounttask($model->task_dep_id) + 1;
                    }
                },
            ],
        ];
    }

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
            [['task_dep_id', 'task_name', 'task_direct', 'task_actualtime', 'task_type', 'task_progress', ], 'required'],
            [['task_dep_id', 'task_num', 'task_type', 'task_numchanges', 'task_progress', 'task_active', ], 'integer'],
            [['task_direct', 'task_name', 'task_reasonchanges', 'task_summary'], 'string'],
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
            'task_actualtime' => 'Реальный срок',
            'task_numchanges' => 'Изменения',
            'task_reasonchanges' => 'Причина',
            'task_progress' => 'Статус',
            'task_summary' => 'Отчет',
            'task_active' => 'Удалена',
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


    /**
     * Все статусы
     *
     * @return array
     */
    public static function getAllProgresses() {
        return [
            self::PROGRESS_STOP => self::PROGRESS_TEXT_STOP,
            self::PROGRESS_WORK => self::PROGRESS_TEXT_WORK,
            self::PROGRESS_FINISH => self::PROGRESS_TEXT_FINISH,
            self::PROGRESS_WAIT => self::PROGRESS_TEXT_WAIT,
        ];
    }

    /**
     * Тип задачи
     *
     * @return string
     */
    public function getTaskProgress() {
        $a = self::getAllProgresses();
        return isset($a[$this->task_progress]) ? $a[$this->task_progress] : '';
    }

    /**
     *
     * Отношение задачи к отделу
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment() {
        return $this->hasOne(Department::className(), ['dep_id' => 'task_dep_id']);
    }

    /**
     *
     * Подсчет задач отдела
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getCounttask($depId = 0) {
        $query = self::find();
        if( $depId != 0 ) {
            $query->where(['task_dep_id' => $depId]);
        }


        return $query->max('task_num');
    }

    /**
     *
     * Установка отдела по пользователю
     *
     * @param User $user
     * @return \yii\db\ActiveQuery
     */
    public function setDepartmentByUser($user = null) {
        if( !Yii::$app->user->can('createUser') ) {
            if( $user ) {
                $this->task_dep_id = $user->us_dep_id;
            }
            else if( Yii::$app->user->identity ) {
                $this->task_dep_id = Yii::$app->user->identity->us_dep_id;
            }
        }
    }
}
