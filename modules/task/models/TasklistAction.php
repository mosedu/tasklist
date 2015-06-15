<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 15.06.2015
 * Time: 11:07
 */

namespace app\modules\task\models;

use app\modules\task\models\Action;

class TasklistAction extends Action {

    const TYPE_OF_ACTION = 'Tasklist';

    public function __construct($scenario='insert'){
        // устанавливаем наш тип полю базового класса
        $this->type_of_action = self::TYPE_OF_ACTION;
        parent::__construct($scenario);
    }
}