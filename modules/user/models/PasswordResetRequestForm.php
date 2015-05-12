<?php
namespace app\modules\user\models;

use yii;
use yii\base\Model;
use app\modules\user\models\User;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\app\modules\user\models\User',
                'targetAttribute' => 'us_email',
                'filter' => ['us_active' => User::STATUS_ACTIVE],
                'message' => 'Такой пользователь не найден'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return boolean whether the email was send
     */
    public function sendEmail()
    {
        /* @var $user User */
        $user = User::findOne([
            'us_active' => User::STATUS_ACTIVE,
            'us_email' => $this->email,
        ]);

        if ($user) {
            if (!User::isPasswordResetTokenValid($user->us_password_reset_token)) {
                $user->generatePasswordResetToken();
            }
            $user->scenario = 'passwordop';

            if( $user->save() ) {
//                return \Yii::$app->mailer->compose(['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'], ['user' => $user])
                $aMessages = [];
                $oMsg = Yii::$app->mailer->compose('passResetToken', ['user' => $user])
                    ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Восстановление пароля на сайте ' . \Yii::$app->name);
                SwiftHeaders::serAntiSpamHeaders($oMsg);
                $bRet = $oMsg->send();
/*                $oMsg = \Yii::$app->mailer->compose('passwordResetToken-html', ['user' => $user])
                    ->setFrom([\Yii::$app->params['supportEmail'] => \Yii::$app->name])
                    ->setTo($this->email)
                    ->setSubject('Восстановление пароля на сайте ' . \Yii::$app->name);
                $bRet = $oMsg->send();
                $aMessages = [$oMsg];
*/
//                $bRet = Yii::$app->mailer->sendMultiple($aMessages);
                if( !$bRet ) {
                    \Yii::error("Error send email to [PasswordResetRequestForm::sendEmail()] " . $this->email);
                }
                return $bRet;
            }
            else {
                \Yii::error(print_r($user->getErrors(), true));
            }
        }

        return false;
    }
}
