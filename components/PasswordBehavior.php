<?php
/**
 * User: KozminVA
 * Date: 02.03.2015
 * Time: 11:59
 */

namespace app\components;

use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

use app\modules\user\models\User;

/**
 * @property string $attributes аттрибут с паролем
 */
class PasswordBehavior extends Behavior
{
    /**
     * @var string аттрибут с паролем
     */
    public $attributes = [];

    /**
     * @var string шаблон письма о пароле
     */
    public $template = '';

    /**
     * @var string тема письма о пароле
     */
    public $subject = '';

    /**
     * Назначаем обработчик для [[owner]] событий.
     * @return array События (array keys) с назначеными им обработчиками (array values).
     */
    public function events()
    {
//        $events = parent::events();
        $events = [];
        foreach ($this->attributes as $i => $event) {
            $events[$i] = 'setNewPassword';
        }
        return $events;
    }

    /**
     * Создаем пароль
     * @param Event $event Текущее событие.
     */
    public function setNewPassword($event)
    {
        if ( isset($this->attributes[$event->name]) && $this->attributes[$event->name] ) {
            $sPassword = substr(str_replace(['_', '-'], ['', ''], Yii::$app->security->generateRandomString()), 0, 8);
            /** @var User $model */
            $model = $this->owner;
            $model->newPassword = $sPassword;

            $model->setPassword($sPassword);
            $model->generateAuthKey();
            if( $this->template !== null ) {
                $template = empty($this->template) ? 'user_create_info' : $this->template;
                $subject = empty($this->subject) ? ('Уведомление портала ' . Yii::$app->name) : $this->subject;
                $model->sendNotificate($template, $subject, ['model' => $model]);
            }
        }
    }

}