<?php

namespace app\components;

use Yii;
use Closure;
use yii\base\Behavior;
use yii\base\Event;

/**
 * AttributewalkBehavior назначает аттрибуты объекта ActiveRecord для определенными значениями .
 *
 * Использовать как стандартное AttributeBehavior, только отличие в том, что функция выполняется персонально
 * для каждого аттрибута и в нее дополнительно передается имя этого аттрибута
 *
 * ~~~
 * use yii\behaviors\AttributeBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => AttributeBehavior::className(),
 *             'attributes' => [
 *                 ActiveRecord::EVENT_BEFORE_INSERT => 'attribute1',
 *                 ActiveRecord::EVENT_BEFORE_UPDATE => 'attribute2',
 *             ],
 *             'value' => function ($event, $attribute) {
 *                 return 'some value';
 *             },
 *         ],
 *     ];
 * }
 * ~~~
 *
 * User: KozminVA
 * Date: 05.03.2015
 * Time: 12:44
 */


class AttributewalkBehavior extends Behavior {
    /**
     * @var array список аттрибутов, которым будет присвоено значение [[value]].
     * Ключики массива - события ActiveRecord, для которых аттрибуты будут изменяться,
     * а значения - соответствующие аттрибуты, которые будут изменяться, может быть строкой, может быть массивом строк
     * Например,
     *
     * ```php
     * [
     *     ActiveRecord::EVENT_BEFORE_INSERT => ['attribute1', 'attribute2'],
     *     ActiveRecord::EVENT_BEFORE_UPDATE => 'attribute2',
     * ]
     * ```
     */
    public $attributes = [];
    /**
     * @var mixed Значение, которое будет присвоено аттрибутам. Может быть анонимной функцией, может быть значением
     * Будет выполняться персонально для каждого аттрибута
     * Описание функции такое:
     *
     * ```php
     * function ($event, $attribute)
     * {
     *     // возвращаемое значение будет присвоено атрибуту
     * }
     * ```
     */
    public $value;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return array_fill_keys(array_keys($this->attributes), 'evaluateAttributes');
    }

    /**
     * Evaluates the attribute value and assigns it to the current attributes.
     * @param Event $event
     */
    public function evaluateAttributes($event)
    {
        if (!empty($this->attributes[$event->name])) {
            $attributes = (array) $this->attributes[$event->name];
            foreach ($attributes as $attribute) {
                // ignore attribute names which are not string (e.g. when set by TimestampBehavior::updatedAtAttribute)
                if (is_string($attribute)) {
                    $value = $this->getValue($event, $attribute);
                    $this->owner->$attribute = $value;
                }
            }
        }
    }

    /**
     * Returns the value of the current attributes.
     * This method is called by [[evaluateAttributes()]]. Its return value will be assigned
     * to the attributes corresponding to the triggering event.
     * @param Event $event the event that triggers the current attribute updating.
     * @param string $attribute the current attribute name.
     * @return mixed the attribute value
     */
    protected function getValue($event, $attribute)
    {
        return $this->value instanceof Closure ? call_user_func($this->value, $event, $attribute) : $this->value;
    }
}