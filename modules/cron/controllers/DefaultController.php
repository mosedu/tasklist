<?php

namespace app\modules\cron\controllers;

use app\modules\task\models\Taskflag;
use yii;
use yii\web\Controller;
use yii\web\Response;

use app\modules\user\models\User;
use app\modules\cron\models\Crontab;
Use app\modules\task\models\Tasklist;

class DefaultController extends Controller
{
    public function actionIndex()
    {
        return ''; // $this->render('index');
    }

    /**
     * Оповещение о приближающихся задачах по sms
     * @return array
     */
    public function actionSmsnotify()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $this->finishCron();

        return [];
    }

    /**
     * Оповещение о приближающихся задачах по email
     * @return array
     */
    public function actionFinalnotify() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $nType = Yii::$app->request->getQueryParam('type', 0);
        Yii::info('Finalnotify: nType = ' . $nType);
        $aWhere = [
            1 => [
                    'flag' => Taskflag::FLAG_SEND_3_DAY_EMAIL,
                    'where' => [['tf_flag' => Taskflag::FLAG_SEND_3_DAY_EMAIL, ]],
                    'days' => 3,
                ],
            2 => [
                    'flag' => Taskflag::FLAG_SEND_1_DAY_EMAIL,
                    'where' => ['tf_flag' => Taskflag::FLAG_SEND_1_DAY_EMAIL, ],
                    'days' => 1,
                ],
        ];

        if( !isset($aWhere[$nType]) ) {
            return [];
        }

        $a = Tasklist::getExpireTasks([
            'days' => $aWhere[$nType]['days'],
//            'join' => 'flags',
//            'where' => $aWhere[$nType]['where'],
        ]);

        $r = [];
        /** @var Tasklist $ob */
        foreach($a As $ob) {
//            $r[] = $ob;
//            $r[] = $ob->flags;
            $bContinue = false;
            foreach($ob->flags As $oFlag) {
                // если уже есть такой флаг - значит, отправили уведомление и пропускаем задачу.
                if( $oFlag->tf_flag == $aWhere[$nType]['flag'] ) {
                    $bContinue = true;
                    break;
                }
            }
            if( $bContinue ) {
//                Yii::info('Finalnotify: ' . $ob->task_id . ' ---');
                continue;
            }
            Yii::info('Finalnotify: task_id = ' . $ob->task_id . ' +++');

            foreach($ob->workersdata As $oUser) {
                /** @var User $oUser*/
                $oUser->sendNotificate('user_task_expire', 'Приближается срок окончания задачи ' . $ob->getTasknum() . ' ' . $ob->task_name, ['task' => $ob]);
            }
            $r[] = ['id' => $ob->task_id, 'uc' => count($ob->workersdata)];

            Taskflag::setTaskFlags($ob->task_id, $aWhere[$nType]['flag']);
        }

        $this->finishCron();

        return $r;
    }

    /**
     *
     */
    public function finishCron() {
        $session = Yii::$app->session;
        $idTask = $session->get('cronid', 0);
        Yii::info('Cron task need to finish: ' . $idTask . ': clear session');
        Crontab::finishTask($idTask);
        $session->remove('cronid');
    }

}
