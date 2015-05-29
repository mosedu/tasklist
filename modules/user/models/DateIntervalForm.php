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
            [['from_date', 'to_date', ], 'required'], // 'department_id', 'user_id',
            [['department_id', 'user_id', ], 'integer'],
//            [['dep_user_roles'], 'in', 'range' => array_keys(User::getUserRoles())],
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
        $sWorkerTable = Worker::tableName();
        $sDepartmentTable = Department::tableName();
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


        $sSql = <<<EOT
Select Distinct ta.task_id, ta.task_dep_id, ta.task_createtime, ta.task_finaltime, ta.task_finishtime
    , IF((ta.task_finishtime Is Not Null And (ta.task_finishtime <= ta.task_finaltime Or ta.task_finaltime > '{$sFinish}')), 1, 0) As ok
From {$sTaskTable} ta
Left Join {$sWorkerTable} wk On wk.worker_task_id = ta.task_id
Where ta.task_createtime < '{$sFinish}' And ta.task_finaltime > '{$sStart}' And (ta.task_finishtime > '{$sStart}' Or ta.task_finishtime Is Null ) {$sDop}
EOT;
        $reader = Yii::$app->db->createCommand($sSql, $aParam)->query();
        foreach($reader As $k=>$v) {
            $aData[] = $v;
        }
        Yii::info(print_r($aData, true));

        return $aData;
    }


}