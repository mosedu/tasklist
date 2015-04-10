<?php

namespace app\modules\task\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use yii\helpers\Html;

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

    const FINTIME_INTERVAL = 604800; //  24 * 3600 * 7, диапазн до даты task_finaltime, когда нужно покрасить ячейку этой даты

    public $_oldAttributes = [];
    public $reasonchange = ''; // причина изменения даты


    public function behaviors()
    {
        return [
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['task_dep_id', 'task_actualtime', 'task_finaltime', 'task_active', 'task_createtime', 'task_num', ],
                    ActiveRecord::EVENT_AFTER_FIND => ['_oldAttributes', ],
                ],
                /** @var Event $event */
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

                        case '_oldAttributes':
                            $model->task_actualtime = date('d.m.Y', strtotime($model->task_actualtime));
                            return $this->getTaskattibutes();
                    }
                },
            ],
            // сохраним предыдущие аттрибуты
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_FIND => '_oldAttributes',
                ],
                'value' => function ($event) {
                    /** @var Tasklist $model */
                    $model = $event->sender;
                    return $model->getTaskattibutes();
                },
            ],

            // проверим изменение аттрибутов
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['_oldAttributes', 'task_actualtime']
                ],
                'value' => function ($event, $attribute) {
                    /** @var Tasklist $model */
                    $model = $event->sender;
                    switch($attribute) {
                        case 'task_actualtime':
                            return date('Y-m-d H:i:s', strtotime($model->task_actualtime) + 24 * 3600 - 1);

                        case '_oldAttributes':
                            $aChanged = $model->getChangeattibutes();
                            if (isset($aChanged['task_actualtime'])) {
                                Yii::info('task_actualtime CHANGED: ' . $aChanged['task_actualtime']['old'] . ' -> ' . $aChanged['task_actualtime']['new']);
                                $model->task_numchanges++;
                                $model->task_reasonchanges .= "{$aChanged['task_actualtime']['old']} -> {$aChanged['task_actualtime']['new']}\t" . $model->reasonchange . "\t" . Yii::$app->user->getId() . "\n";
                            }
                            if (isset($aChanged['task_progress']) && ($aChanged['task_progress']['new'] == Tasklist::PROGRESS_FINISH) ) {
                                $this->task_actualtime = date('Y-m-d 00:00:00');
                            }
                            if( count($aChanged) > 0 ) {
                                // TODO: тут поставить логирование кто и что изменил
                            }
                            return $model->_oldAttributes;
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
            [['task_type', 'task_progress', 'task_active', ], 'filter', 'filter' => 'intval'],
            [['task_dep_id', 'task_name', 'task_direct', 'task_actualtime', 'task_type', 'task_progress', ], 'required'],
            [['reasonchange', ], 'required',
                'when' => function($model) { return $model->task_actualtime != $model->_oldAttributes['task_actualtime']; },
                'whenClient' => "function (attribute, value) {
                 console.log(jQuery('#".Html::getInputId($this, 'reasonchange')."').attr('data-old') + ' ? ' + '".$this->_oldAttributes['task_actualtime']."');
                return jQuery('#".Html::getInputId($this, 'reasonchange')."').attr('data-old') != '".$this->_oldAttributes['task_actualtime']."'; }",
            ],
            [['task_summary', ], 'required',
                'when' => function($model) { return $model->task_progress == Tasklist::PROGRESS_FINISH; },
                'whenClient' => "function (attribute, value) { return jQuery('#".Html::getInputId($this, 'task_summary')."').attr('data-req') == 1; }",
            ],
            [['task_dep_id', 'task_num', 'task_type', 'task_numchanges', 'task_progress', 'task_active', ], 'integer'],
            [['task_direct', 'task_name', 'task_reasonchanges', 'task_summary', 'reasonchange'], 'string'],
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
            'reasonchange' => 'Причина переноса',
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

    /**
     *
     * Получение цветов для выделения
     *
     * @return array
     */
    public function getStatusAttributes() {
        $aRet = [
            'fintimeclass' => '',
            'acttimeclass' => '',
        ];
        $diff = strtotime($this->task_finaltime) - time();
        if( $this->task_progress != Tasklist::PROGRESS_FINISH ) {
            $aRet['fintimeclass'] = ( $diff < 0 ) ? ' colorcell_red' : (( $diff < self::FINTIME_INTERVAL ) ? ' colorcell_yellow' : '');
        }
        else {
            $sfin = date('Ymd', strtotime($this->task_finaltime));
            $sact = date('Ymd', strtotime($this->task_actualtime));
            $aRet['acttimeclass'] = ( $sact < $sfin ) ? ' colorcell_green' : (( $sact > $sfin ) ? ' colorcell_red' : '');
        }
    }

    /**
     * Получение атрибутов модели
     *
     * @return array
     */
    public function getTaskattibutes() {
        return $this->attributes;
    }

    /**
     * Получение измененных аттрибутов модели
     *
     * @return array
     */
    public function getChangeattibutes() {
        $aChanged = [];
        $aNewAttr = $this->getTaskattibutes();
        foreach($aNewAttr As $k=>$v) {
            if( !isset($this->_oldAttributes) ) {
                $aChanged[$k] = [
                    'old' => null,
                    'new' => $v,
                ];
            }
            else {
                if( $this->_oldAttributes[$k] !== $v ) {
                    $aChanged[$k] = [
                        'old' => $this->_oldAttributes[$k],
                        'new' => $v,
                    ];
                }
            }
        }
        if( count($aChanged) > 0 ) {
            Yii::info('getChangeattibutes(): ' . print_r($aChanged, true));
        }

        return $aChanged;
    }


}
