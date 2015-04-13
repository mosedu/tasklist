<?php
/**
 * User: KozminVA
 * Date: 01.04.2015
 * Time: 15:39
 *
 * проверка соответствия департамента пользователя и задачи
 *
 */

namespace app\rbac;

use yii\rbac\Rule;
use yii\filters\AccessRule;
use app\modules\user\models\User;

class DepartmentRule extends Rule {
    public $name = 'isDepartment';
    public $params = [];

    /**
     * @param integer $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return boolean a value indicating whether the rule permits the role or permission it is associated with.
     */

    public function execute($user, $item, $params)
    {
        /** @var app\modules\user\models\User $user */
        \Yii::info('user = ' . print_r($user, true));
        $oUser = User::findOne($user);
        if( !$oUser ) {
            return false;
        }
        \Yii::info('user = ' . print_r($oUser->attributes, true));
        \Yii::info('item = ' . print_r($item, true));
        \Yii::info('params = ' . implode(', ', array_keys($params)) . print_r(isset($params['task']) ? $params['task']->attributes : [], true) );
        $bRet = isset($params['task']) ? (($params['task']->task_dep_id == $oUser->us_dep_id) || ($oUser->us_dep_id == 1)) : false;
        \Yii::info('DepartmentRule(): return ' . ($bRet ? 'true' : 'false') );
        return $bRet;
    }
}