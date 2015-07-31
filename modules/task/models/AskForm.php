<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 30.07.2015
 * Time: 17:29
 */

namespace app\modules\task\models;

use Yii;
use yii\base\Model;

class AskForm extends Model {
    public $text;
    public $buttons = [
        0 => ['text' => 'Да', 'class' => 'btn btn-success'],
        1 => ['text' => 'Нет', 'class' => 'btn btn-danger'],
    ];
    public $pressed;

    public function rules()
    {
        return [
            [['pressed'], 'required'],
            [['pressed'], 'in', 'range' => array_keys($this->buttons)],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'pressed' => 'Нажатая кнопка',
        ];
    }

    /**
     * @return boolean
     */
    public function isAttribute($attrName)
    {
        if( in_array($attrName, ['exec']) ) {
            return false;
        }
        return true;
    }
    /**
     * @return array command result
     */
    public function runCommand()
    {
        $k = $this->pressed;
        if( is_array($this->buttons[$k]) && isset($this->buttons[$k]['exec']) ) {
            $sRet = call_user_func($this->buttons[$k]['exec']);
            if( is_string($sRet) && (strlen($sRet) > 0) ) {
                return [
                    'error' => $sRet,
                ];
            }
            else if( isset($this->buttons[$k]['js']) ) {
                return [
                    'js' => $this->buttons[$k]['js'],
                ];

            }
        }
        return [];
    }

}