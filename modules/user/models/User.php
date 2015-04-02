<?php

namespace app\modules\user\models;

use Yii;
use yii\web\IdentityInterface;
use app\components\AttributewalkBehavior;
use app\components\PasswordBehavior;
use app\modules\user\models\Department;
use yii\db\Expression;
use yii\db\ActiveRecord;
use yii\rbac\Role;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $us_id
 * @property integer $us_active
 * @property integer $us_dep_id
 * @property string $us_email
 * @property string $us_password_hash
 * @property string $us_name
 * @property string $us_secondname
 * @property string $us_lastname
 * @property string $us_login
 * @property string $us_logintime
 * @property string $us_createtime
 * @property string $us_workposition
 * @property string $us_auth_key
 * @property string $us_email_confirm_token
 * @property string $us_password_reset_token
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

    const STATUS_TEXT_DELETED = 'Удален';
    const STATUS_TEXT_ACTIVE = 'Активен';
    const STATUS_TEXT_WAIT = 'Карантин';

    const ROLE_DEPARTMENT = 'department';
    const ROLE_CONTROL = 'control';
    const ROLE_ADMIN = 'admin';

    const ROLE_TEXT_DEPARTMENT = 'Обычный';
    const ROLE_TEXT_CONTROL = 'Контроль';
    const ROLE_TEXT_ADMIN = 'Админ';

    public $newPassword = '';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors(){
        $a = [
            // при добавлении пользователя
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['us_active', 'us_createtime', 'us_login'],
                ],
                'value' => function ($event, $attribute) {
                    /** @var yii\base\Event $event */
                    /** @var app\modules\user\models\User $model */
                    $model = $event->sender;
                    $aVal = [
                        'us_active' => self::STATUS_ACTIVE,
                        'us_createtime' => new Expression('NOW()'),
                    ];
                    if( empty($model->us_login) ) {
                        $aVal['us_login'] = preg_replace('/\\W/', '', $model->us_email);
                    }
                    if( isset($aVal[$attribute]) ) {
                        return $aVal[$attribute];
                    }
                    return null;
                },
            ],

            // добавляем роль пользователю после сохранения
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_INSERT => ['us_dep_id'],
                ],
                'value' => function ($event, $attribute) {
                    /** @var User $model */
                    $model = $event->sender;
                    $auth = Yii::$app->authManager;
                    if( !empty($model->us_dep_id) ) {
                        /** @var Role $role */
                        $role = Department::getDepartmentrole($model->us_dep_id);
                        Yii::info($event->name . ': ' . $model->us_dep_id . ' -> ' . (($role !== null) ? $role->name : 'null'));
                        if( $role !== null ) {
                            $auth->assign($role, $model->us_id);
                            Yii::info($event->name . ': assign ' . $role->name . ' to ' . $model->us_id);
                        }
                    }
                    return $model->us_dep_id;
                },
            ],

            // устанавливаем пароль для нового пользователя и отправляем ему письмо
            'passwordBehavior' => [
                'class' => PasswordBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => true,
                ],
                'template' => 'user_create_info',
                'subject' => 'Регистрация на портале ' . Yii::$app->name,
            ],
        ];

        return $a;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['us_active', 'us_dep_id'], 'integer'],
            [['us_email', 'us_name'], 'required'],
            [['us_logintime', 'us_createtime'], 'safe'],
            [['us_email', ], 'email', ],
            [['us_email', 'us_password_hash', 'us_name', 'us_secondname', 'us_lastname', 'us_login', 'us_workposition', 'us_email_confirm_token', 'us_password_reset_token'], 'string', 'max' => 255],
            [['us_auth_key'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'us_id' => 'id',
            'us_active' => 'Активен',
            'us_dep_id' => 'Отдел',
            'us_email' => 'Email',
            'us_password_hash' => 'Password Hash',
            'us_name' => 'Имя',
            'us_secondname' => 'Отчество',
            'us_lastname' => 'Фамилия',
            'us_login' => 'Логин',
            'us_logintime' => 'Logintime',
            'us_createtime' => 'Createtime',
            'us_workposition' => 'Должность',
            'us_auth_key' => 'Auth Key',
            'us_email_confirm_token' => 'Email Confirm Token',
            'us_password_reset_token' => 'Password Reset Token',
        ];
    }

    /**************************************************************************************************************
     *     Start IdentityInterface methods
     *************************************************************************************************************/

    public static function findIdentity($id)
    {
        return static::find()
            ->where([
                'us_id' => $id,
                'us_active' => self::STATUS_ACTIVE,
            ])
            ->with('department')
            ->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()
            ->where([
                'us_auth_key' => $token,
                'us_active' => self::STATUS_ACTIVE,
            ])
            ->with('department')
            ->one();
    }

    public function getId()
    {
        return $this->us_id;
    }

    public function getAuthKey()
    {
        return $this->us_auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->us_auth_key === $authKey;
    }

    /*************************************************************************************************************
     *      Finish IdentityInterface methods
     *************************************************************************************************************/

    /**
     * Получение пользователя по логину
     * @param string $userlogin логин пользователя
     * @return null|static
     */
    public static function findByUserlogin($userlogin) {
        return static::findOne(['us_login' => $userlogin, 'us_active' => self::STATUS_ACTIVE]);
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $bRet =false;
        if( preg_match('/^\$2[axy]\$(\d\d)\$[\.\/0-9A-Za-z]{22}/', $this->us_password_hash, $matches) && $matches[1] >= 4 && $matches[1] <= 30) {
            $bRet = Yii::$app->security->validatePassword($password, $this->us_password_hash);
        }
        else {
            Yii::warning(User::className() . "::validatePassword({$password}): ERROR us_password_hash not match REGEXP");
        }
//        Yii::warning("validatePassword({$password}): " . ($bRet ? 'yes' : 'no'));
        return $bRet;
    }

    /**
     * Getter user name for identity
     * @return string
     */
    public function getUsername() {
        return $this->us_login;
    }

    /**
     *
     * Отношение пользователя к отделу
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment() {
        return $this->hasOne(Department::className(), ['dep_id' => 'us_dep_id']);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->us_password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->us_auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->us_password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->us_password_reset_token = null;
    }

    /**
     * @param string $email_confirm_token
     * @return static|null
     */
    public static function findByEmailConfirmToken($email_confirm_token)
    {
        return static::findOne(['us_email_confirm_token' => $email_confirm_token, 'us_active' => self::STATUS_WAIT]);
    }

    /**
     * Generates email confirmation token
     */
    public function generateEmailConfirmToken()
    {
        $this->us_email_confirm_token = Yii::$app->security->generateRandomString();
    }

    /**
     * Removes email confirmation token
     */
    public function removeEmailConfirmToken()
    {
        $this->us_email_confirm_token = null;
    }

    /**
     *  Полное имя пользователя
     */
    public function getFullName() {
        $s = trim($this->us_name . ' ' . $this->us_secondname);
        if( $s == '' ) {
            $s = $this->us_login;
        }
        return $this->us_lastname . ' ' . $s;
    }

    /**
     *  Имя, фамилия пользователя
     */
    public function getShortName() {
        $s = trim($this->us_name . ' ' . $this->us_secondname);
        if( $s == '' ) {
            $s = $this->us_lastname;
        }
        return $s;
    }

    /**
     * Отправка письма пользователю
     *
     * @param string $template имя шаблона письма
     * @param string $subject тема письма
     * @param array $data данные для письма
     */
    public function sendNotificate($template, $subject = '', $data = []) {
        if( $subject === '' ) {
            $subject = 'Уведомление портала ' . Yii::$app->name;
        }
        Yii::$app->mailer->compose($template, ['model' => $this,])
            ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name])
            ->setTo($this->us_email)
            ->setSubject($subject)
            ->send();
    }

    /**
     * Получение списка ролей
     * @return array список ролей - ключ - id роли, значение - заголовок для отображения
     */
    public static function getUserRoles()
    {
        return [
            self::ROLE_DEPARTMENT => self::ROLE_TEXT_DEPARTMENT,
            self::ROLE_CONTROL => self::ROLE_TEXT_CONTROL,
            self::ROLE_ADMIN => self::ROLE_TEXT_ADMIN,

        ];
    }

    /**
     * Получение названия роли
     * @param string $role
     * @return string
     */
    public static function getRoleTitle($role)
    {
        $a = self::getUserRoles();
        return isset($a[$role]) ? $a[$role] : '';
    }

    /**
     * Получение списка статусов
     * @return array список статусов - ключ - id статуса, значение - заголовок для отображения
     */
    public static function getUserStatuses()
    {
        return [
            self::STATUS_DELETED => self::STATUS_TEXT_DELETED,
            self::STATUS_ACTIVE => self::STATUS_TEXT_ACTIVE,
            self::STATUS_WAIT => self::STATUS_TEXT_WAIT,
        ];
    }

    /**
     * Получение статуса
     * @return string
     */
    public function getUserStatus()
    {
        $a = [
            self::STATUS_ACTIVE => self::STATUS_TEXT_ACTIVE,
            self::STATUS_DELETED => self::STATUS_TEXT_DELETED,
            self::STATUS_WAIT => self::STATUS_TEXT_WAIT,
        ];

        $a = [
            self::STATUS_ACTIVE => '+',
            self::STATUS_DELETED => '-',
            self::STATUS_WAIT => '?',
        ];

        return isset($a[$this->us_active]) ? $a[$this->us_active] : '~';
    }

}
