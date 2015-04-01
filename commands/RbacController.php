<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionCreate()
    {
        /** @var \yii\rbac\DbManager $auth */
        $auth = Yii::$app->authManager;
        $a = [
            $auth->assignmentTable,
            $auth->itemChildTable,
            $auth->itemTable,
            $auth->ruleTable,
        ];

        Yii::$app->db->createCommand('set foreign_key_checks=0')->execute();
        foreach($a As $table) {
//            Yii::$app->db->createCommand('Delete From ' . $table . ' Where TRUE')->execute();
            Yii::$app->db->createCommand()->truncateTable($table)->execute();
        }
        Yii::$app->db->createCommand('set foreign_key_checks=1')->execute();
//        return;

        // ----------------------------------------------------       Добавление прав на пользователей
        $createUser = $auth->createPermission('createUser');
        $createUser->description = 'Создать пользователя';
        $auth->add($createUser);

        $updateUser = $auth->createPermission('updateUser');
        $updateUser->description = 'Изменить пользователя';
        $auth->add($updateUser);

        // ----------------------------------------------------       Добавление прав на задачи
        $createTask = $auth->createPermission('createTask');
        $createTask->description = 'Создать задачу';
        $auth->add($createTask);

        $updateTask = $auth->createPermission('updateTask');
        $updateTask->description = 'Изменить задачу';
        $auth->add($updateTask);

        $ruleDepart = new \app\rbac\DepartmentRule; // проверка соответствия департамента пользователя и задачи
        $auth->add($ruleDepart);

        $updateDepartTask = $auth->createPermission('updateDepartTask');
        $updateDepartTask->description = 'Изменить задачу своего отдела';
        $updateDepartTask->ruleName = $ruleDepart->name;

        $auth->add($updateDepartTask);
        $auth->addChild($updateDepartTask, $updateTask);

        // ----------------------------------------------------       Добавление ролей пользователей
        $departmentUser = $auth->createRole('departmentUser');
        $departmentUser->description = 'Пользователь подразделения';
        $auth->add($departmentUser);
        $auth->addChild($departmentUser, $createTask);
        $auth->addChild($departmentUser, $updateTask);

        $controlUser = $auth->createRole('control');
        $controlUser->description = 'Пользователь отдела мониторинга';
        $auth->add($controlUser);
        $auth->addChild($controlUser, $createTask);
        $auth->addChild($controlUser, $updateDepartTask);
        $auth->addChild($controlUser, $createUser);
        $auth->addChild($controlUser, $updateUser);

        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $departmentUser->description = 'Администратор';
        $auth->addChild($admin, $controlUser);
        $auth->addChild($admin, $departmentUser);
        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        //// usually implemented in your User model.

//        $auth->assign($departmentUser, 2);
        $auth->assign($admin, 1);
//        return $this->render('create');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
