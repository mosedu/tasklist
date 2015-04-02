<?php

namespace app\modules\user\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%department}}".
 *
 * @property integer $dep_id
 * @property string $dep_name
 * @property string $dep_shortname
 * @property string $dep_user_roles
 * @property integer $dep_active
 */
class Department extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_TEXT_DELETED = 'Удален';
    const STATUS_TEXT_ACTIVE = 'Активен';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%department}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dep_name'], 'required'],
            [['dep_active'], 'integer'],
            [['dep_name', 'dep_shortname', 'dep_user_roles'], 'string', 'max' => 255],
//            [['dep_user_roles'], 'in', 'range' => array_keys(User::getUserRoles())],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dep_id' => 'id',
            'dep_name' => 'Полное наименование',
            'dep_shortname' => 'Краткое наименование',
            'dep_active' => 'Статус',
            'dep_user_roles' => 'Права пользователей отдела',
        ];
    }

    /**
     * Получение массива отделов, ключ - id, значение - название отдела
     *
     * @param boolean $bShort : true - короткое название, false - полное название
     * @return array
     */
    public static function getList($bShort = true) {
        return ArrayHelper::map(
            self::find()->where(['dep_active' => self::STATUS_ACTIVE])->all(),
            'dep_id',
            $bShort ? 'dep_shortname' : 'dep_name'
        );
    }

    /**
     * Получение роли отдела по его id
     *
     * @param integer $id : true - короткое название, false - полное название
     * @return array
     */
    public static function getDepartmentrole($id = 0) {
        $model = self::findOne($id);
        if( ($model === null) || ($model->dep_user_roles === null) ) {
            Yii::info("getDepartmentrole({$id}) : null");
            return null;
        }
        Yii::info("getDepartmentrole({$id}) : find Role {$model->dep_user_roles}");
        return Yii::$app->authManager->getRole($model->dep_user_roles);
    }

    /**
     * Получение списка статусов
     * @return array список статусов - ключ - id статуса, значение - заголовок для отображения
     */
    public static function getDepStatuses()
    {
        return [
            self::STATUS_DELETED => self::STATUS_TEXT_DELETED,
            self::STATUS_ACTIVE => self::STATUS_TEXT_ACTIVE,
        ];
    }

    /**
     * Получение статуса
     * @return string
     */
    public function getDepStatus()
    {
        $a = [
            self::STATUS_ACTIVE => self::STATUS_TEXT_ACTIVE,
            self::STATUS_DELETED => self::STATUS_TEXT_DELETED,
        ];

        return isset($a[$this->dep_active]) ? $a[$this->dep_active] : '~';
    }
}
