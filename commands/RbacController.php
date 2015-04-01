<?php

namespace app\commands;

use Yii;
use yii\console\Controller;

class RbacController extends Controller
{
    public function actionCreate()
    {
        $auth = Yii::$app->authManager;
        // add "createTask" permission
        $createTask = $auth->createPermission('createTask');
        $createTask->description = 'Создать задачу';
        $auth->add($createTask);

        // add "updateTask" permission
        $updateTask = $auth->createPermission('updateTask');
        $updateTask->description = 'Изменить задачу';
        $auth->add($updateTask);


        // add "author" role and give this role the "createPost" permission
        $author = $auth->createRole('author');
        $auth->add($author);
        $auth->addChild($author, $createPost);
        // add "admin" role and give this role the "updatePost" permission
        //// as well as the permissions of the "author" role
        $admin = $auth->createRole('admin');
        $auth->add($admin);
        $auth->addChild($admin, $updatePost);
        $auth->addChild($admin, $author);
        // Assign roles to users. 1 and 2 are IDs returned by IdentityInterface::getId()
        //// usually implemented in your User model.

        $auth->assign($author, 2);
        $auth->assign($admin, 1);
        return $this->render('create');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

}
