<?php

namespace app\modules\task\models;

use app\modules\user\models\User;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\base\Event;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\db\Query;
use yii\validators\RangeValidator;
use yii\validators\Validator;
use yii\web\UploadedFile;

use app\components\AttributewalkBehavior;
use app\modules\user\models\Department;
use app\modules\task\models\Action;
use app\modules\task\models\Changes;
use app\modules\task\models\Worker;
use app\components\NotifyBehavior;
use app\modules\task\models\Taskflag;
use yii\helpers\Url;

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
 * @property string $task_finaltime  // первая дата завершения
 * @property string $task_actualtime // дата завершения после переносов
 * @property string $task_finishtime // реальная дата завершения
 * @property integer $task_numchanges
 * @property string $task_reasonchanges
 * @property integer $task_progress
 * @property integer $task_worker_id
 * @property string $task_summary
 * @property string $task_expectation
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
    public $curworkers = []; // список id сотрудников в задаче

    private  $_workersid = null; // возможные сотрудники
    private  $_lastWorkersDepartment = null; // последний отдел, по которому искались мотрудники - проверяем, нужен ли еще запрос

    public $_canEdit = null; // сохранение проверки возможности редактирования
//    public $countworker = ''; //

    public static $_tmpModel = null;

    public function behaviors()
    {
        return [
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['task_dep_id', 'task_actualtime', 'task_finaltime', 'task_active', 'task_createtime', 'task_num', /*'task_worker_id', */'task_finishtime', ],
                    ActiveRecord::EVENT_AFTER_FIND => ['curworkers', '_oldAttributes', ],
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

                        case 'task_finishtime':
                            // если создаем законченную задачу, то присваиваем ей дату завершения
                            if( $model->task_progress == Tasklist::PROGRESS_FINISH ) {
                                return $model->task_actualtime;
                            }
                            else {
                                return $model->task_finishtime;
                            }

                        case 'task_dep_id':
                            $model->setDepartmentByUser();
                            return $model->task_dep_id;

                        case 'task_createtime':
                            return new Expression('NOW()');

//                        case 'task_worker_id':
//                            if( in_array(Yii::$app->user->identity->us_role_name, array_keys(User::getWorkerRoles())) ) {
//                                return Yii::$app->user->identity->us_id;
//                            }
//                            return $model->task_worker_id;

                        case 'task_active':
                            return self::STATUS_ACTIVE;

                        case 'task_num':
                            return Tasklist::getCounttask($model->task_dep_id) + 1;

                        case '_oldAttributes':
                            $model->task_actualtime = date('d.m.Y', strtotime($model->task_actualtime));
                            return $this->getTaskattibutes();

                        case 'curworkers':
                            return ArrayHelper::map($this->workers, 'worker_us_id', 'worker_us_id');
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
                            if( isset($aChanged['task_worker_id']) ) {
                                if( ($oUser = User::findOne($aChanged['task_worker_id']['new'])) !== null ) {
                                    /** @var User $oUser */
                                    $oUser->sendNotificate('user_new_task', 'Новая задача', ['task'=>$model]);
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
                        $model->saveWorkers();
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
                        $model->saveWorkers();
                    }
                    return $model->_oldAttributes;
                },
            ],

            // Отправка писем при создании задачи
            [
                'class' =>  NotifyBehavior::className(),
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
            [
                ['task_expectation', ],
                'required',
                'when' => function ($model) { return $model->isNewRecord; },
                'whenClient' => "function (attribute, value) { return " . ($this->isNewRecord ? 'true' : 'false') . "; }"
            ],

            [['curworkers'], 'filter', 'filter' => function($val){ if( is_string($val) ) { if( trim($val) == '' ) { $val = []; } else { $val = [intval($val)]; } } else if( is_array($val) ) { foreach($val As $k=>$v) { if( trim($v) == '' ) { unset($val[$k]); } else { $val[$k] = intval($v); } } } return $val; }],
//            [['curworkers'], 'in', 'range' => array_keys($this->getTaskAvailWokers()), 'allowArray' => true],
            [['curworkers'], 'isWorkersInDepartment'],
            [['task_summary', ], 'required',
                'when' => function($model) { return $model->task_progress == Tasklist::PROGRESS_FINISH; },
                'whenClient' => "function (attribute, value) { return jQuery('#".Html::getInputId($this, 'task_summary')."').attr('data-req') == 1; }",
            ],
//            ['task_worker_id', 'filter', 'filter' => 'intval', ],
            [['task_dep_id', 'task_num', 'task_type', 'task_numchanges', 'task_progress', 'task_active', /*'task_worker_id',*/ ], 'integer'],
            [['task_direct', 'task_name', 'task_reasonchanges', 'task_summary', 'task_expectation', ], 'string'], // 'reasonchange'
            [['task_createtime', 'task_finaltime', ], 'safe'] // 'task_actualtime'
        ];

        if( !$this->canChangeDate() && !$this->isNewRecord ) {
//            $aRules[] = [['task_direct', ], 'required'];

            $aRules[] =
            [['task_reasonchanges', ], 'required',
               // причина нужна когда дату переносим дальше
               'when' => function($model) { return implode('', array_reverse(explode('.', $model->task_actualtime))) > implode('', array_reverse(explode('.', $model->_oldAttributes['task_actualtime']))); },
                // whenClient не используется, если нужен, то его надо тоже переделать как и 'when'
               'whenClient' => "function (attribute, value) {
                    console.log('data-old = ' + jQuery('#".Html::getInputId($this, 'task_reasonchanges')."').attr('data-old') + ' ? ' + '".$this->_oldAttributes['task_actualtime']."');
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

    public function isWorkersInDepartment($attribute, $params) {
        $aUsers = $this->getTaskAvailWokers(true);
        $aKeys = array_keys($aUsers);
        $val = $this->$attribute;
        if( !is_array($val) ) {
            $val = [$val];
        }
        foreach($val As $v) {
            if (!in_array($v, $aKeys)) {
                $this->addError($attribute, 'Значение поля "' . $this->getAttributeLabel($attribute) . '" (' . $aUsers[$v] . ') неверно');
            }
        }
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
            'task_worker_id' => 'Сотрудник',
            'curworkers' => 'Сотрудники',
            'task_expectation' => 'Ожидаемый результат',
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
     * Отношение задачи к сотруднику
     *
     * @return \yii\db\ActiveQuery
     */
//    public function getWorker() {
//        return $this->hasOne(User::className(), ['us_id' => 'task_worker_id']);
//    }

    /**
     *
     * Отношение задачи к сотруднику
     *
     * @return \yii\db\ActiveQuery
     */

    public function getAllworker() {
        return $this
//            ->hasOne(User::className(), ['us_dep_id' => 'task_dep_id'])
            ->hasMany(User::className(), ['us_dep_id' => 'task_dep_id'])
            ->where(['us_active' => User::STATUS_ACTIVE, ]); // 'us_role_name' => User::ROLE_WORKER,
    }

    /**
     *
     * Возможные сотрудники - список id
     *
     * @return array
     */
    public function getTaskAvailWokers($bRefresh = false) {
        if( ($this->_workersid === null) || $bRefresh ) {
            if( $this->_lastWorkersDepartment !== $this->task_dep_id ) { // чтобы поменьше запросов делать
                $aWhere = ['us_active' => User::STATUS_ACTIVE, ];
                if (!empty($this->task_dep_id)) {
                    $aWhere['us_dep_id'] = $this->task_dep_id;
                }
                $this->_workersid = ArrayHelper::map(
                    User::find()->where($aWhere)->all(),
                    'us_id',
                    function ($ob) {
                        return $ob->getFullName();
                    }
                );
                $this->_lastWorkersDepartment = $this->task_dep_id;
//                Yii::info("getTaskAvailWokers(): " . print_r($this->getTaskAvailWokers(), true));
            }
        }
        return $this->_workersid;
    }

    /**
     *
     * Отношение задачи к сотруднику в модели, когда много ответственных
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkers() {
        return $this->hasMany(Worker::className(), ['worker_task_id' => 'task_id']);
    }

    /**
     *
     * Отношение задачи к сотруднику в модели, когда много ответственных
     *
     * @return \yii\db\ActiveQuery
     */
    public function getWorkersdata() {
        return $this->hasMany(User::className(), ['us_id' => 'worker_us_id'])->via('workers');
    }

    /**
     *
     * Отношение задачи к файлам
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTaskfiles() {
        return $this->hasMany(File::className(), ['file_task_id' => 'task_id'])->orderBy('file_time');
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
     * Отношение задачи к флагам
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFlags() {
        return $this->hasMany(Taskflag::className(), ['tf_task_id' => 'task_id']);
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
                if( empty($this->curworkers) ) {
                    $this->curworkers = [$user->us_id => $user->us_id, ];
                }
            }
            else if( Yii::$app->user->identity ) {
                $this->task_dep_id = Yii::$app->user->identity->us_dep_id;
                if( empty($this->curworkers) ) {
                    $this->curworkers = [Yii::$app->user->identity->us_id => Yii::$app->user->identity->us_id];
                }
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
        $a['curworkers'] = array_values($this->curworkers);
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

        if( Yii::$app->user->identity->isUserWorker() ) {
//            $query->andFilterWhere(['task_worker_id' => Yii::$app->user->identity->us_id]);
            $query->join('LEFT JOIN', '{{%worker}}', 'worker_task_id = task_id');
            // LEFT JOIN `tlst_worker` ON `tlst_tasklist`.`task_id` = `tlst_worker`.`worker_task_id`
            $query->andFilterWhere(['in', Worker::tableName() . '.worker_us_id', Yii::$app->user->identity->us_id]);
        }
        else if( $nRole == User::ROLE_DEPARTMENT ) {
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
//        Yii::info('canChangeErrorDate(): ' . $this->task_createtime);
        $bRet = false;
        if( $this->task_createtime !== null ) {
            if( time() - strtotime($this->task_createtime) <= self::DATE_CHANGE_INTERVAL ) {
                $bRet = true;
            }
        }
        return $bRet;
    }

    /**
     * Проверка на возможность изменения задачи
     *
     * @return boolean
     */
    public function canEdit() {
        if( $this->_canEdit === null ) {
            $bRet = false;
            $oUser = Yii::$app->user->identity;
            if( $this->task_dep_id == $oUser->us_dep_id ) {
                if( ($oUser->us_role_name == User::ROLE_DEPARTMENT)
                    || Yii::$app->user->can(User::ROLE_CONTROL)
                    || (in_array($oUser->us_role_name, array_keys(User::getWorkerRoles())) && in_array($oUser->us_id, $this->curworkers)/*$this->task_worker_id == $oUser->us_id*/) ) {
                    $bRet = true;
                }
            }
            else {
                if( Yii::$app->user->can(User::ROLE_CONTROL) || Yii::$app->user->can(User::ROLE_ADMIN) ) {
                    $bRet = true;
                }
            }
            $this->_canEdit = $bRet;
        }
        return $this->_canEdit;
    }

    /**
     * Получение Url на просмотр задачи
     *
     * $param boolean $bFull
     * @return string
     */
    public function url($bFull = true) {
        return Url::to(['/task/default/view/' . $this->task_id], $bFull);
    }

    /**
     * Сохранение соответствующих сотрудников в задаче
     *
     */
    public function saveWorkers() {
        $aDel = []; // То, что надо будет удалить
        foreach($this->workers As $ob) {
            $nKey = array_search($ob->worker_us_id, $this->curworkers);
            if( $nKey === false ) {
                // сотрудника удалили из списка
                $aDel[] = $ob;
//                Yii::info('saveWorkers(): need del ' . $ob->worker_us_id);
            }
            else {
                unset($this->curworkers[$nKey]);
//                Yii::info('saveWorkers(): go ' . $ob->worker_us_id);
            }
        }
        $ob = reset($aDel);
        foreach($this->curworkers As $v) {
            if($ob === false) {
                // если нечего взять из старых записей, создаем новую
                $ob = new Worker();
                $ob->worker_task_id = $this->task_id;
//                Yii::info('saveWorkers(): add record for ' . $v);
            }
            $ob->worker_us_id = $v;
            if( !$ob->save() ) {
                Yii::error('Error save worker records: ' . print_r($ob->getErrors(), true));
            }
            $ob = next($aDel);
        }

        while( $ob ) {
            // удаляем остатки старого
//            Yii::info('saveWorkers(): delete ' . $ob->worker_us_id);
            $ob->delete();
            $ob = next($aDel);
        }

    }

    /**
     * Process upload of file
     *
     */
    public function uploadFiles($aData) {
//        Yii::info("loadFiles() = " . print_r(UploadedFile::getFiles(), true));

        $aModels = $this->taskfiles;
        $aNeedDel = [];
        foreach($aModels As $oFile) {
            $nIndex = $this->searchFile($aData['data'], $oFile->file_id);
//            Yii::info('saveRoles('.$uid.'): find: ' . print_r($obData->attributes, true) . ' = ' . (( $aRoleData !== null ) ? '' : 'not ') . 'exists');
            if( $nIndex !== -1 ) {
                // все нормально, запись остается
//                Yii::info('uploadFiles('.$oFile->file_id.'): ['.$nIndex.'] ' . $oFile->file_comment . ' -> ' . $aData['data'][$nIndex]['file_comment']);

                $oFile = File::findOne($aData['data'][$nIndex]['file_id']);
                $oFile->file_comment = $aData['data'][$nIndex]['file_comment'];
                $oFile->save();
                unset($aData['data'][$nIndex]);
            }
            else {
                Yii::info('uploadFiles('.$oFile->file_id.'): need del');
                $aNeedDel[] = $oFile;
//                Yii::info('saveRoles('.$uid.'): delete');
            }
        }

        // оставшиеся данные пытаемся запихать в существующие записи, которые должны быть удалены
        $oFile = reset($aNeedDel);
        foreach($aData['data'] As $k=>$data) {
//            Yii::info('uploadFiles(): data ' . $k . ' ' . print_r($data, true));
            if( $oFile === false ) {
                // кончились удаляемые записи - создаем новые
                $oFile = new File();
//                Yii::info('uploadFiles(): new File');
            }
            $ob = UploadedFile::getInstance(new File(), '['.$k.']filedata');
            $oFile->setDataByUpload(
                $ob,
                $this->task_id,
                $data['file_comment'],
                $data['file_group']
            );

            if( !$oFile->save() ) {
                Yii::error("uploadFiles() Error save: " . print_r($oFile->getErrors(), true));
            }
            else {
                $ob->saveAs($oFile->getFullpath());
            }
            $oFile = next($aNeedDel);
        }

        // если остались удаляемые записи - удаляем их
        while( $oFile !== false ) {
//            Yii::error("uploadFiles() Delete File");
            $oFile->delete();
            $oFile = next($aNeedDel);
        }

    }

    /**
     * @param array $data post data for files
     * @param int $id file id for search
     * @return int index in data
     */
    public function searchFile($data, $id) {
        $index = -1;
        foreach($data As $k=>$v) {
            if( $v['file_id'] == $id ) {
                $index = $k;
                break;
            }
        }
        return $index;
    }

    /**
     * Поиск задач, заканчивающихся через $nDays рабочих дней, праздники не учитываем
     * @param array $aParam параметры выборки задач:
     *      'days' - количество дней до окончания задачи
     *      'isworkdays' - считаем рабочие дни (true) или любые (false)
     *      'curdate' - дата, от которой считаем срок 'days'
     * @return array
     */
    public static function getExpireTasks($aParam) {
        /**
         * @var int $nDays
         * @var boolean $bWorkdays
         * @var int $curDate
         */
        $aDefault = [
            'days' => 3,
            'isworkdays' => true,
            'curdate' => time(),
        ];

        $aParam = array_merge($aDefault, $aParam);

        Yii::info('getExpireTasks(): ' . print_r($aParam, true));

        $curDate = $aParam['curdate'];
        $nDays = $aParam['days'];
        $bWorkdays = $aParam['isworkdays'];

        $diff = $nDays * 24 * 3600;
        $t = $curDate + $diff;

        if( $bWorkdays ) {
            if( $nDays > 5 ) {
                $t += floor($nDays / 5) * 2 * 24 * 3600;
            }
            $wd = date('N', $t);
            if(($wd == 6) || ($wd == 7)) {
                $t += 2 * 24 * 3600;
            }
            else if( $wd < date('N', $curDate) ) {
                $t += 2 * 24 * 3600;
            }
        }

        $query = self::find()
            ->with(['workersdata', 'flags'])
            ->where(
                array_merge(
                    [
                        'and',
                        ['not in', 'task_progress', Tasklist::PROGRESS_FINISH],
                        ['=', 'DATE(task_actualtime)', date('Y-m-d', $t)],
                    ],
                    isset($aParam['where']) ? $aParam['where'] : []
                )
            );

        if( isset($aParam['join']) ) {
            $query->joinWith($aParam['join'], true);
        }

        return $query->all();
    }
}
