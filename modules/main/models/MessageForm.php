<?php

namespace app\modules\main\models;

use Yii;
use yii\base\Model;
use app\modules\user\models\User;

/**
 * ContactForm is the model behind the contact form.
 */
class MessageForm extends Model
{
    public $subject;
    public $body;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['body'], 'required'], // 'subject',
            [['body'], 'mytest'], // 'subject',
        ];
    }

    public function mytest() {
        $this->addError('body', 'Incorrect username or password.');
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'body' => 'Текст сообщения',
        ];
    }

    /**
     * Sends an email to the specified email address using the information collected by this model.
     * @return boolean whether the model passes validation
     */
    public function contact()
    {
        /** @var User $oUser */
        $oUser = Yii::$app->user->identity;
        if( isset(Yii::$app->params['contactEmail']) ) {
            Yii::$app->mailer->compose()
                ->setTo(Yii::$app->params['contactEmail'])
                ->setFrom([$oUser->us_email => $oUser->getFullName()])
                ->setReplyTo([$oUser->us_email => $oUser->getFullName()])
                ->setSubject('Сообщение с сайта ' . $_SERVER['HTTP_HOST'])
                ->setTextBody($this->body)
                ->send();
        }
        return true;
    }

}
