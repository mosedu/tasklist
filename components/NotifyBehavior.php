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
        $curId = Yii::$app->user->identity->getId();
        $sTitle = 'Новая задача в Системе мониторинга задач ГАУ ТемоЦентр';
        $sTemplate = 'new_task';

        foreach($aUsers As $ob) {
            /** @var User $ob */
            if( $ob->us_id != $curId ) {
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
}