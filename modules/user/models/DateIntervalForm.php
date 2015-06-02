<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 29.05.2015
 * Time: 14:55
 */

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use app\modules\task\models\Tasklist;
use app\modules\user\models\User;
use app\modules\user\models\Department;
use app\modules\task\models\Worker;
Use app\modules\task\models\Changes;

class DateIntervalForm extends Model {
    public $from_date;
    public $to_date;
    public $department_id;
    public $user_id;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['department_id', ],
                'filter',
                'filter' => [$this, 'filterDepartment'],
            ],
            [
                ['user_id', ],
                'filter',
                'filter' => [$this, 'filterUser'],
            ],
            [['from_date', 'to_date', ], 'required'], // 'department_id', 'user_id',
//            [['dep_user_roles'], 'in', 'range' => array_keys(User::getUserRoles())],
            [['department_id', 'user_id', ], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'from_date' => 'Начало периода',
            'to_date' => 'Окончание периода',
            'department_id' => 'Отдел',
            'user_id' => 'Сотрудник',
        ];
    }

    /**
     *
     */
    public function filterDepartment($value = ""){
        if( !Yii::$app->user->can('createUser') && (Yii::$app->user->identity->us_dep_id != 1) ) {
            $value = Yii::$app->user->identity->us_dep_id;
        }
        return $value;
    }

    /**
     *
     */
    public function filterUser($value = ""){
        if( !Yii::$app->user->can('createUser') && ( Yii::$app->user->identity->us_dep_id != 1 ) && ( !Yii::$app->user->can('department') ) ) {
            $value = Yii::$app->user->getId();
        }
        return $value;
    }

    /**
     *
     */
    public function setdefaultValues()
    {
        $this->department_id = $this->filterDepartment();
        $this->user_id = $this->filterUser();
    }

    /**
     *
     */
    public function mkTime($sDate)
    {
        $a = explode('.', $sDate);
        return mktime(0, 0, 0, $a[1], $a[0], $a[2]);
    }

    /**
     *
     * Calculate KPI
     *
     * @return array
     */
    public function calcKpi() {
        $sTaskTable = Tasklist::tableName();
        $sUserTable = User::tableName();
        $sChangesTable = Changes::tableName();
        $sWorkerTable = Worker::tableName();
        $sDepartmentTable = Department::tableName();
        $nTaksActiveFlag = Tasklist::STATUS_ACTIVE;
        $sStart = date('Y-m-d H:i:s', $this->mkTime($this->from_date) - 1);
        $sFinish = date('Y-m-d H:i:s', $this->mkTime($this->to_date) + 24 * 3600);
        $sDop = "";
        $aParam = [];
        $aData = [];

        if( $this->department_id != '' ) {
            $sp = ':depid';
            $sDop .= " And ta.task_dep_id = {$sp}";
            $aParam[$sp] = $this->department_id;
        }

        if( $this->user_id != '' ) {
            $sp = ':userid';
            $sDop .= " And wk.worker_us_id = {$sp}";
            $aParam[$sp] = $this->user_id;
        }
//        Left Join {$sDepartmentTable} dep dep.dep_id = ta.task_dep_id

// Среднее количество задач считаем так:
// суммируем количество дней по каждой задаче, в течении которых она была активна в требуемый интервал
// делим на количество дней в периоде - получим среднее в день
// Для завершенной задачи берем верхнюю границу для вычисления среднего количества задач - минимальную из верхней границы диапазона и времени окончания
//    , IF(ta.task_finishtime Is Not Null, IF(ta.task_finishtime < '{$sFinish}', ta.task_finishtime, '{$sFinish}'), '{$sFinish}') As fdate
//    , IF(ta.task_createtime > '{$sStart}', ta.task_createtime, '{$sStart}') As sdate
// Distinct
        $sSql = <<<EOT
Select ta.task_id
    , ta.task_dep_id
    , ta.task_type
    , ta.task_createtime
    , ta.task_finaltime
    , ta.task_finishtime
    , IF((ta.task_finishtime Is Not Null And (ta.task_finishtime <= ta.task_finaltime Or ta.task_finaltime > '{$sFinish}') Or (ta.task_finishtime Is Null And ta.task_finaltime > '{$sFinish}')), 1, 0) As ok
    , DATEDIFF(IF(ta.task_finishtime Is Not Null, IF(ta.task_finishtime < '{$sFinish}', ta.task_finishtime, '{$sFinish}'), '{$sFinish}'), IF(ta.task_createtime > '{$sStart}', ta.task_createtime, '{$sStart}'))-1 As ndays
    , DATEDIFF('{$sFinish}', '{$sStart}') - 1 As nperioddays
    , t1.changes
From {$sTaskTable} ta
Left Join {$sWorkerTable} wk On wk.worker_task_id = ta.task_id
Left Join ( Select ch_task_id, COUNT(cn.ch_id) As changes From {$sChangesTable} cn Group By ch_task_id) t1 On t1.ch_task_id = ta.task_id
Left Join {$sChangesTable} cn On cn.ch_task_id = ta.task_id
Where ta.task_createtime < '{$sFinish}'
  And ta.task_finaltime > '{$sStart}'
  And (ta.task_finishtime > '{$sStart}' Or ta.task_finishtime Is Null )
  And (ta.task_active = {$nTaksActiveFlag} )
  {$sDop}
Group By ta.task_id
EOT;
        $reader = Yii::$app->db->createCommand($sSql, $aParam)->query();
        foreach($reader As $k=>$v) {
            $aData[] = $v;
        }
        Yii::info(print_r($aData, true));

        return $aData;
    }


}