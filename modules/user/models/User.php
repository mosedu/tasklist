<?php

namespace app\modules\user\models;

use Yii;
use yii\web\IdentityInterface;
use app\modules\user\models\Department;

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
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_WAIT = 2;

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
    public function rules()
    {
        return [
            [['us_active', 'us_dep_id'], 'integer'],
            [['us_email', 'us_password_hash', 'us_name', 'us_createtime'], 'required'],
            [['us_logintime', 'us_createtime'], 'safe'],
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
        return $this->hasOne(Department::className(), ['id' => 'us_dep_id']);
    }
}
