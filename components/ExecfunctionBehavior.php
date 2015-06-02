<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 02.06.2015
 * Time: 12:47
 */

namespace app\components;

use Yii;
use yii\db\ActiveRecord;
use yii\base\Behavior;
use yii\base\Event;
use yii\base\InvalidParamException;

class ExecfunctionBehavior extends Behavior {

    public $function_events = [];

    /**
     * @var Closure null
     *
     * function($model, $event) {
     * }
     *
     */
    public $function_def = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if( $this->function_def === null ) {
            throw new InvalidParamException("Need set 'function_def' parameter");
        }

//        if( !($this->function_def instanceof Closure) ) {
//            throw new InvalidParamException("Parameter 'function_def' has to be function(\$model, \$event)");
//        }
    }

    public function events()
    {
        $events = [];
        foreach ($this->function_events as $event) {
//             ? call_user_func($this->value, $event) : $this->value;
            $events[$event] = 'evaluate';
        }
        return $events;
    }

    public function evaluate($event)
    {
        /**
         * @var Event $event
         */
        call_user_func($this->function_def, $event->sender, $event);
    }


}