<?php

use yii\db\Schema;
use yii\db\Migration;
use app\modules\user\models\User;

class m150506_093458_add_worker extends Migration
{
    public function up()
    {
        $auth = Yii::$app->authManager;


        $createWorker = $auth->getPermission('createWorker');
        if( $createWorker === null ) {
            $createWorker = $auth->createPermission('createWorker');
            $createWorker->description = 'Создать сотрудника';
            $auth->add($createWorker);
        }

        $depUser = $auth->getRole(User::ROLE_DEPARTMENT);
        $auth->addChild($depUser, $createWorker);

        $controlUser = $auth->getRole(User::ROLE_CONTROL);
        $auth->addChild($controlUser, $createWorker);

        $admin = $auth->getRole(User::ROLE_ADMIN);
        $auth->addChild($admin, $createWorker);

        $updateTask = $auth->getPermission('updateTask');

        $departmentWorker = $auth->createRole(User::ROLE_WORKER);
        $departmentWorker->description = 'Сотрудник подразделения';
        $auth->add($departmentWorker);
        $auth->addChild($departmentWorker, $updateTask);

        $this->addColumn('{{%tasklist}}', 'task_worker_id', Schema::TYPE_INTEGER);
        $this->refreshCache();
    }

    public function down()
    {
//        echo "m150506_093458_add_worker cannot be reverted.\n";
        $auth = Yii::$app->authManager;

        $createWorker = $auth->getPermission('createWorker');
        $controlUser = $auth->getRole(User::ROLE_CONTROL);
        $depUser = $auth->getRole(User::ROLE_DEPARTMENT);
        $admin = $auth->getRole(User::ROLE_ADMIN);
        $departmentWorker = $auth->getRole(User::ROLE_WORKER);

        if( $createWorker !== null ) {
            $auth->removeChild($controlUser, $createWorker);
            $auth->removeChild($depUser, $createWorker);
            $auth->removeChild($admin, $createWorker);
            $auth->removeChild($departmentWorker, $createWorker);
            $auth->remove($createWorker);
            $auth->remove($departmentWorker);
        }

        $this->dropColumn('{{%tasklist}}', 'task_worker_id');
        $this->refreshCache();

        return true;
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
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
