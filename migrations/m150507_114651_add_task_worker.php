<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\user\models\User;

class m150507_114651_add_task_worker extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;

        $createTask = $auth->getPermission('createTask');
        $updateTask = $auth->getPermission('updateTask');

        $createTaskWorker = $auth->getRole(User::ROLE_TASKWORKER);
        if( $createTaskWorker === null ) {
            $createTaskWorker = $auth->createRole(User::ROLE_TASKWORKER);
            $createTaskWorker->description = 'Сотрудник, создающий задачи';
            $auth->add($createTaskWorker);
        }

        $auth->addChild($createTaskWorker, $createTask);
        $auth->addChild($createTaskWorker, $updateTask);

    }

    public function down()
    {
        $auth = Yii::$app->authManager;

        $createTask = $auth->getPermission('createTask');
        $updateTask = $auth->getPermission('updateTask');
        $createTaskWorker = $auth->getRole(User::ROLE_TASKWORKER);

        $auth->removeChild($createTaskWorker, $createTask);
        $auth->removeChild($createTaskWorker, $updateTask);

        $auth->remove($createTaskWorker);

        return true;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
