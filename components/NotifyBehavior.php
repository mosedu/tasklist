<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 06.05.2015
 * Time: 9:29
 */

namespace app\components;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use app\modules\task\models\Tasklist;
use app\modules\user\models\Department;
use app\modules\user\models\User;

class NotifyBehavior extends Behavior {

    public function events() {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'notyfyUsers',
            ActiveRecord::EVENT_AFTER_UPDATE => 'notyfyNewUsers',
        ];
    }

    /**
     * @param Event $event
     */
    public function notyfyUsers($event) {
        /** @var Tasklist $model */
        $model = $event->sender;
        $dep = $model->department;
//        Yii::info('Department: ' . print_r($dep->attributes, true));
        $aUsers = $dep->leaders;
//        Yii::info('aUsers: ' . count($aUsers));
        $curId = Yii::$app->user->identity->getId();
        $sTitle = 'Новая задача в Системе мониторинга задач ГАУ ТемоЦентр';
        $sTemplate = 'new_task';
        $nUserNotify = 0;
        /*
                if( !empty($model->task_worker_id) ) {
                    $nUserNotify = $model->task_worker_id;
                    $model->worker->sendNotificate('user_new_task', $sTitle, ['task' => $model, 'department' => $dep]);
                }
        */
        if( !empty($model->curworkers) ) {
            foreach( User::find()
                         ->where(['us_id' => $model->curworkers])
                         ->all() As $ob) {
                $ob->sendNotificate('user_new_task', $sTitle, ['task' => $model, 'department' => $dep]);
            }
        }

        foreach($aUsers As $ob) {
            Yii::info('for: '.$ob->us_id.' != ' . $curId);
            /** @var User $ob */
//            if( ($ob->us_id != $curId) && ($nUserNotify != $ob->us_id) ) {
            if( ($ob->us_id != $curId) && ( !in_array($ob->us_id, $model->curworkers)) ) {
                $aNotify[$ob->us_id] = $ob->us_id;
                $ob->sendNotificate($sTemplate, $sTitle, ['task' => $model, 'department' => $dep]);
            }
        }

        // Тут письма в отдел контроля
                /** @var User $ob */
/*
        if( $model->department->dep_id != 1 ) {
            $aUsers = User::find()
                ->where([
                    'us_dep_id' => 1,
                    'us_active' => User::STATUS_ACTIVE,
                ])
                ->all();
            foreach($aUsers As $ob) {
                if( $ob->us_id != $curId ) {
                    $ob->sendNotificate($sTemplate, $sTitle, ['task' => $model, 'department' => $dep]);
                }
            }
        }
*/
    }

    /**
     * @param Event $event
     */
    public function notyfyNewUsers($event) {
        /** @var Tasklist $model */
        $model = $event->sender;
        $dep = $model->department;
        $sTitle = 'Новая задача в Системе мониторинга задач ГАУ ТемоЦентр';
        $aChanged = $model->getChangeattibutes();
        if( isset($aChanged['curworkers']) ) {
            $aDiff = array_diff($aChanged['curworkers']['new'], $aChanged['curworkers']['old']);
            Yii::info('Worker diff: ' . print_r($aDiff, true));
            foreach( User::find()
                         ->where(['us_id' => $aDiff])
                         ->all() As $ob) {
                $ob->sendNotificate('user_new_task', $sTitle, ['task' => $model, 'department' => $dep]);
            }
        }
    }

}