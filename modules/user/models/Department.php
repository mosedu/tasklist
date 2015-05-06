<?php

namespace app\modules\user\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use yii\base\Event;

use app\components\AttributewalkBehavior;
use app\modules\user\models\User;

/**
 * This is the model class for table "{{%department}}".
 *
 * @property integer $dep_id
 * @property string $dep_name
 * @property string $dep_shortname
 * @property string $dep_user_roles
 * @property integer $dep_active
 * @property integer $dep_num
 */
class Department extends \yii\db\ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    const STATUS_TEXT_DELETED = 'Удален';
    const STATUS_TEXT_ACTIVE = 'Активен';

    public static $_map = null;

    public function behaviors()
    {
        return [
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['dep_num', 'dep_active'],
                ],
                /** @var Event $event */
                'value' => function ($event, $attribute) {
                    /** @var Department $model */
                    $model = $event->sender;
                    switch($attribute) {
                        case 'dep_num':
                            return Department::getMaxnum() + 1;
                        case 'dep_active':
                            return self::STATUS_ACTIVE;
                    }
                },
            ],
            [
                'class' =>  AttributewalkBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_AFTER_UPDATE => ['dep_user_roles'],
                ],
                /** @var Event $event */
                'value' => function ($event, $attribute) {
                    /** @var Department $model */
                    $model = $event->sender;
                    switch($attribute) {
                        case 'dep_user_roles':
                            $aUser = User::find()->where(['us_dep_id' => $model->dep_id])->all();
                            foreach($aUser As $ob) {
                                /** @var User $ob */
                                $ob->save();
                            }
                            return $model->dep_user_roles;
                    }
                },
            ],
        ];
    }

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
            [['dep_active', 'dep_num', ], 'integer'],
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
            'dep_num' => 'Номер',
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

    /**
     *
     * Подсчет максимального номера отдела
     *
     * @return \yii\db\ActiveQuery
     */
    public static function getMaxnum() {
        return self::find()->max('dep_num');
    }

    /**
     *
     * Получение id преыдущей записи по dep_num
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrevByNum() {
        $sTable = self::tableName();
        return Yii::$app->db->createCommand(
            'Select dep_id'
            . ' From ' . $sTable
            . ' Where dep_num In ( Select MAX(dep_num) From ' . $sTable . ' Where dep_num < :num)',
            [':num' => $this->dep_num]
        )
        ->queryScalar();
    }

    /**
     *
     * Получение id следующей записи по dep_num
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNextByNum() {
        $sTable = self::tableName();
        return Yii::$app->db->createCommand(
            'Select dep_id'
            . ' From ' . $sTable
            . ' Where dep_num In ( Select MIN(dep_num) From ' . $sTable . ' Where dep_num > :num)',
            [':num' => $this->dep_num]
        )
        ->queryScalar();
    }

    /**
     * @inheritdoc
     */
    public static function getDepartmentName($id)
    {
        if( self::$_map === null ) {
            self::$_map = ArrayHelper::map(self::find()->all(), 'dep_id', 'dep_name');
        }
        return isset(self::$_map[$id]) ? self::$_map[$id] : '??';
    }

    /**
     * @param string $sName
     * @return integer
     */
    public static function getDepartmentIdByName($sName)
    {
        $ob = self::find()->where(['dep_name' => $sName])->one();
        if( $ob === null ) {
            $aAdmName = ['Отдел мониторинга и контроля'];
            $ob = new Department();
            $ob->dep_name = $sName;
            $ob->dep_shortname = $sName;
            $ob->dep_user_roles = in_array($sName, $aAdmName) ? User::ROLE_CONTROL : User::ROLE_DEPARTMENT;
            if( !$ob->save() ) {
                Yii::warning('getDepartmentIdByName('.$sName.') ERROR ADD NEW DEP: ' . print_r($ob->getErrors(), true));
                $ob->dep_id = 0;
            }
        }
        return $ob->dep_id;
    }

    public function getLeaders() {
        $query = $this->hasMany(
            User::className(),
            [
                'us_dep_id' => 'dep_id',
            ]
        );
        $query->where([
            'us_active' => User::STATUS_ACTIVE,
            'us_role_name' => User::ROLE_DEPARTMENT,
        ]);
        return $query;
    }
}
