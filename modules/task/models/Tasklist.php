<?php

namespace app\modules\task\models;

use app\modules\user\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use yii\helpers\Html;
use yii\db\Query;

use app\components\AttributewalkBehavior;
use app\modules\user\models\Department;
use app\modules\task\models\Action;
use app\modules\task\models\Changes;

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
 * @property string $task_finishtime
 * @property integer $task_numchanges
 * @property string $task_reasonchanges
 * @property integer $task_progress
 * @property string $task_summary
 * @property integer $task_active
 */
class Tasklist extends \yii\db\ActiveRecord
{
    const DATE_CHANGE_INTERVAL = 86400;

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

    public static $_tmpModel = null;


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
                            $sDate = date('Y-m-d H:i:s', strtotime($model->task_actualtime) + 24 * 3600 - 1);
                            if( $model->canChangeDate() ) {
                                $model->task_finaltime = $sDate;
                            }
                            return $sDate;

                        case '_oldAttributes':
                            $aChanged = $model->getChangeattibutes();
                            if ( !$model->canChangeDate() && isset($aChanged['task_actualtime'])) {
                                Yii::info('task_actualtime CHANGED: ' . $aChanged['task_actualtime']['old'] . ' -> ' . $aChanged['task_actualtime']['new']);
                                if( preg_match('|^(\\d+)\\.(\\d+)\\.(\\d+)$|', $aChanged['task_actualtime']['old'], $aOld)
                                 && preg_match('|^(\\d+)\\.(\\d+)\\.(\\d+)$|', $aChanged['task_actualtime']['new'], $aNew)) {
                                    // меняем счетчик только при изменении в сторону увеличения
                                    if( $aNew[3] . $aNew[2] . $aNew[1] > $aOld[3] . $aOld[2] . $aOld[1] ) {
                                        if( $model->task_progress != Tasklist::PROGRESS_FINISH ) {
                                            $model->task_numchanges++;
                                        }
                                    }
                                }
                                else {
                                    Yii::info('ERROR: not found date in aChanged[task_actualtime]: ' . print_r($aChanged['task_actualtime'], true));
                                }
                                // $model->task_reasonchanges .= "{$aChanged['task_actualtime']['old']} -> {$aChanged['task_actualtime']['new']}\t" . str_replace("\n", '\\n', $model->reasonchange) . "\n"; // "\t" . Yii::$app->user->getId() .
                            }
                            else {
//                                $model->task_reasonchanges = $model->_oldAttributes['task_reasonchanges'];
                            }
                            if( isset($aChanged['task_progress']) ) {
                                if( $aChanged['task_progress']['new'] == Tasklist::PROGRESS_FINISH ) {
                                    // завершили задачу - устанавливаем task_finishtime
                                    // $this->task_actualtime = date('Y-m-d 00:00:00');
                                    $this->task_finishtime = date('Y-m-d 00:00:00');
                                }
                                if( $aChanged['task_progress']['old'] == Tasklist::PROGRESS_FINISH ) {
                                    // это опять возобновили законченную задачу - нужно вернуть дату завершения
                                    $this->task_finishtime = null;
                                }
                            }
                            return $model->_oldAttributes;
                    }
                },
            ],

            // логируем данные
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_UPDATE => ['_oldAttributes',],
                    ActiveRecord::EVENT_AFTER_INSERT => ['_oldAttributes',],
                    ActiveRecord::EVENT_AFTER_DELETE => ['_oldAttributes',],
                ],
                'value' => function ($event, $attribute) {
                    /** @var Event $event */
                    /** @var Tasklist $model */
                    $model = $event->sender;
                    $data = [
                        'id' => $model->task_id,
                        'table' => $model->tableName(),
                    ];

                    if( $event->name == ActiveRecord::EVENT_AFTER_INSERT ) {
                        Action::appendCreation($data);
                    }
                    else if( $event->name == ActiveRecord::EVENT_AFTER_UPDATE ) {
                        $aChanged = $model->getChangeattibutes();
                        if( count($aChanged) > 0 ) {
                            if( isset($aChanged['task_active']) ) {
                                Action::appendDelete($data);
                            }
                            else {
                                Action::appendUpdate(array_merge($data, $aChanged));
                                if ( !$model->canChangeDate() && isset($aChanged['task_actualtime']) ) {
                                    if( $model->task_progress != Tasklist::PROGRESS_FINISH ) {
                                        Changes::addChange($model);
                                    }
                                    // $model->task_reasonchanges .= "{$aChanged['task_actualtime']['old']} -> {$aChanged['task_actualtime']['new']}\t" . str_replace("\n", '\\n', $model->reasonchange) . "\n"; // "\t" . Yii::$app->user->getId() .
                                }
                            }
                        }
                    }
                    return $model->_oldAttributes;
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
        $aRules = [
            [['task_dep_id', 'task_type', 'task_progress', 'task_active', ], 'filter', 'filter' => 'intval'],
            [['task_summary', ], 'filter', 'filter'=>'trim'],

            [['task_dep_id', 'task_name', 'task_actualtime', 'task_type', 'task_progress', ], 'required'], // 'task_direct',
            [['task_summary', ], 'required',
                'when' => function($model) { return $model->task_progress == Tasklist::PROGRESS_FINISH; },
                'whenClient' => "function (attribute, value) { return jQuery('#".Html::getInputId($this, 'task_summary')."').attr('data-req') == 1; }",
            ],
            [['task_dep_id', 'task_num', 'task_type', 'task_numchanges', 'task_progress', 'task_active', ], 'integer'],
            [['task_direct', 'task_name', 'task_reasonchanges', 'task_summary', ], 'string'], // 'reasonchange'
            [['task_createtime', 'task_finaltime', 'task_actualtime'], 'safe']
        ];

        if( !$this->canChangeDate() && !$this->isNewRecord ) {
//            $aRules[] = [['task_direct', ], 'required'];

            $aRules[] =
            [['task_reasonchanges', ], 'required',
                'when' => function($model) { return $model->task_actualtime != $model->_oldAttributes['task_actualtime']; },
                'whenClient' => "function (attribute, value) {
                 console.log(jQuery('#".Html::getInputId($this, 'task_reasonchanges')."').attr('data-old') + ' ? ' + '".$this->_oldAttributes['task_actualtime']."');
                return jQuery('#".Html::getInputId($this, 'task_reasonchanges')."').attr('data-old') != '".$this->_oldAttributes['task_actualtime']."'; }",
            ];
/*
            [['reasonchange', ], 'required',
                'when' => function($model) { return $model->task_actualtime != $model->_oldAttributes['task_actualtime']; },
                'whenClient' => "function (attribute, value) {
                 console.log(jQuery('#".Html::getInputId($this, 'reasonchange')."').attr('data-old') + ' ? ' + '".$this->_oldAttributes['task_actualtime']."');
                return jQuery('#".Html::getInputId($this, 'reasonchange')."').attr('data-old') != '".$this->_oldAttributes['task_actualtime']."'; }",
            ];
*/
        }

        return $aRules;
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
            'task_finishtime' => 'Дата завершения',
            'task_finaltime' => 'Базовый срок',
            'task_actualtime' => $this->isNewRecord ? 'Базовый срок' : ($this->task_progress == self::PROGRESS_FINISH ? 'Реальный срок' : 'Новый срок'),
            'task_numchanges' => 'Переносы',
            'task_reasonchanges' => 'Причина переноса',
//            'reasonchange' => 'Причина переноса',
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
    public function getTaskStatus($id = null)
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
        if( $id === null ) {
            $id = $this->us_active;
        }
        return isset($a[$id]) ? $a[$id] : '';
//        return isset($a[$this->us_active]) ? $a[$this->us_active] : '~';
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
    public function getTaskType($id = null) {
        $a = self::getAllTypes();
        if( $id === null ) {
            $id = $this->task_type;
        }
        return isset($a[$id]) ? $a[$id] : '';
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
    public function getTaskProgress($id = null) {
        $a = self::getAllProgresses();
        if( $id === null ) {
            $id = $this->task_progress;
        }
        return isset($a[$id]) ? $a[$id] : '';
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
     * Отношение задачи к смене даты
     *
     * @return \yii\db\ActiveQuery
     */
    public function getChanges() {
        return $this->hasMany(Changes::className(), ['ch_task_id' => 'task_id']);
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
        $a = $this->attributes;
        unset($a['task_numchanges']);
//        unset($a['task_reasonchanges']);
        return $a;
    }

    /**
     * Форматирование аттрибутов
     *
     * @return array
     */
    public function formatTaskattibutes($attribute, $value) {
        if( ($value === '') || ($value === null) ) {
            return '';
        }
        if( $attribute == 'task_finishtime' ) {
            $value = date('d.m.Y', strtotime($value));
        }
        return $value;
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
                if( $k == 'task_actualtime' ) {
                    $v = date('d.m.Y', strtotime($v));
                }
                if( $this->_oldAttributes[$k] !== $v ) {
                    $aChanged[$k] = [
                        'old' => $this->formatTaskattibutes($k, $this->_oldAttributes[$k]), // $this->_oldAttributes[$k],
                        'new' => $this->formatTaskattibutes($k, $v), // $v,
                    ];
                }
            }
        }
//        if( count($aChanged) > 0 ) {
//            Yii::info('getChangeattibutes(): ' . print_r($aChanged, true));
//        }

        return $aChanged;
    }

    /**
     * Получение статистики
     *
     * @return array
     */
    public static function getStatdata($dep_id = null) {
        $nRole = Yii::$app->user->identity->department ? Yii::$app->user->identity->department->dep_user_roles : User::ROLE_ADMIN;
        // $aStat = [];
        /** @var Query $query */
        $query = (new Query())
            ->select([
                'SUM(IF(task_progress = '.self::PROGRESS_WORK.', 1, 0)) As active',
                'SUM(IF(task_progress <> '.self::PROGRESS_FINISH.' And NOW() > task_finaltime, 1, 0)) As defect',
                'SUM(IF(task_progress = '.self::PROGRESS_WAIT.', 1, 0)) As wait',
            ])
            ->from([self::tableName() . ' f'])
            ->where(['task_active' => Tasklist::STATUS_ACTIVE]);
//        ->where('f.fl_id In (' . implode(',', $aStatFlags) . ')')
//            ->groupBy(['f.fl_name', 'f.fl_id', 'f.fl_sname']);

        if( $nRole == User::ROLE_DEPARTMENT ) {
            $query->andFilterWhere(['task_dep_id' => Yii::$app->user->identity->department->dep_id]);
        }
        else if( $dep_id > 0 ) {
            $query->andFilterWhere(['task_dep_id' => $dep_id]);
        }

        $aStat = $query->createCommand()->queryOne();

        return $aStat;
    }

    /**
     * @param array $aData
     * @return string
     */
    public function getChangesLogText($aData)
    {

        $aTitle = $this->attributeLabels();
        return Yii::$app->getView()->renderFile(
            '@app/modules/task/views/default/changes.php',
            [
                'model' => $this,
                'data' => $aData,
                'title' => $aTitle,
            ],
            null
        );
//        return $this->getView()->render('@app/modules/task/views/default/changes', ['model' => $this, 'data' => $aData], null);
/*
        $s = '';
        // TODO: сделать в отдельной View ????
        foreach($aData As $k=>$v) {
            $sDop = '';
            if( isset($aTitle[$k]) ) {
                $sDop .= $aTitle[$k] . ': ';
            }
            else {
                $sDop .= ': ';
            }
            $sDop .= $v['old'] . ' -> ' . $v['new'];
            $s .= ($s !== '' ? "\n" : '') . $sDop;
        }
        return $s;
*/
    }

    /**
     * Получение url
     *
     * @return string
     */
    public function getUrl() {
        return '/task/default/'.$this->task_id;
    }

    /**
     * Получение num
     *
     * @return string
     */
    public function getTasknum() {
        return $this->department->dep_num . '.' . $this->task_num;
    }

    /**
     * Проверка на возможность изменения даты при ошибке
     *
     * @return boolean
     */
    public function canChangeDate() {
        Yii::info('canChangeErrorDate(): ' . $this->task_createtime);
        $bRet = false;
        if( $this->task_createtime !== null ) {
            if( time() - strtotime($this->task_createtime) <= self::DATE_CHANGE_INTERVAL ) {
                $bRet = true;
            }
        }
        return $bRet;
    }


}
