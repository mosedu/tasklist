<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $us_id
 * @property integer $us_active
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
class User extends \yii\db\ActiveRecord
{
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
            [['us_active'], 'integer'],
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
            'us_id' => 'Us ID',
            'us_active' => 'Us Active',
            'us_email' => 'Us Email',
            'us_password_hash' => 'Us Password Hash',
            'us_name' => 'Us Name',
            'us_secondname' => 'Us Secondname',
            'us_lastname' => 'Us Lastname',
            'us_login' => 'Us Login',
            'us_logintime' => 'Us Logintime',
            'us_createtime' => 'Us Createtime',
            'us_workposition' => 'Us Workposition',
            'us_auth_key' => 'Us Auth Key',
            'us_email_confirm_token' => 'Us Email Confirm Token',
            'us_password_reset_token' => 'Us Password Reset Token',
        ];
    }
}
