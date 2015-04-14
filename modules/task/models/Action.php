<?php

namespace app\modules\task\models;

use Yii;
use yii\db\Expression;
use app\modules\user\models\User;

/**
 * This is the model class for table "{{%action}}".
 *
 * @property integer $act_id
 * @property integer $act_us_id
 * @property integer $act_type
 * @property string $act_createtime
 * @property string $act_data
 * @property string $act_table
 * @property string $act_table_pk
 */
class Action extends \yii\db\ActiveRecord
{
    const TYPE_INSERT = 1;
    const TYPE_UPDATE = 2;
    const TYPE_DELETE = 3;

    const TYPE_TEXT_INSERT = "Добавление";
    const TYPE_TEXT_UPDATE = "Изменение";
    const TYPE_TEXT_DELETE = "Удаление";

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%action}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['act_createtime', 'act_us_id', 'act_type', 'act_table_pk', ], 'required'],
            [['act_us_id', 'act_type', 'act_table_pk'], 'integer'],
            [['act_createtime'], 'safe'],
            [['act_data'], 'string'],
            [['act_table'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'act_id' => 'id',
            'act_us_id' => 'Поьзователь',
            'act_type' => 'Операция',
            'act_createtime' => 'Дата',
            'act_data' => 'Изменения',
            'act_table' => 'Таблица',
            'act_table_pk' => 'id',
        ];
    }

    /**
     *
     *
     * @return array
     */
    public static function getAllTypes() {
        return [
            self::TYPE_INSERT => self::TYPE_TEXT_INSERT,
            self::TYPE_UPDATE => self::TYPE_TEXT_UPDATE,
            self::TYPE_DELETE => self::TYPE_TEXT_DELETE,
        ];
    }

    /**
     *
     *
     * @param integer $nType
     * @return string
     */
    public static function getTypeText($nType) {
        $a = self::getAllTypes();
        return isset($a[$nType]) ? $a[$nType] : '';
    }

    /**
     *
     *
     * @param array $data
     * @return array
     */
    public static function getBaseInfo(&$data) {
        $a = [$data['id'], $data['table']];
        unset($data['id']);
        unset($data['table']);
        return $a;
    }

    /**
     * Добавление записи о задаче
     *
     * @param array $data
     */
    public static function appendData($type, $data) {
        $ob = new Action();
        list($id, $table) = self::getBaseInfo($data);

        $ob->attributes = [
            'act_createtime' => new Expression('NOW()'),
            'act_type'       => $type,
            'act_us_id'      => Yii::$app->user->id,
            'act_table'      => $table,
            'act_table_pk'   => $id,
            'act_data'       => (count($data) > 0) ? serialize($data) : '',
        ];
        if( !$ob->save() ) {
            Yii::warning('Error save appendData('.self::getTypeText($type).') log data: ' . print_r($ob->getErrors(), true));
        }

    }

    /**
     * Добавление записи о создании задачи
     *
     * @param array $data
     */
    public static function appendCreation($data) {
        self::appendData(self::TYPE_INSERT, $data);
    }

    /**
     * Добавление записи о удалении задачи
     *
     * @param array $data
     */
    public static function appendDelete($data) {
        self::appendData(self::TYPE_DELETE, $data);
    }

    /**
     * Добавление записи о удалении задачи
     *
     * @param array $data
     */
    public static function appendUpdate($data) {
        self::appendData(self::TYPE_UPDATE, $data);
    }


    /**
     *
     * Отношение лога к задаче
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTask() {
        return $this->hasOne(Tasklist::className(), ['task_id' => 'act_table_pk']);
    }

    /**
     *
     * Отношение лога к пользователю
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser() {
        return $this->hasOne(User::className(), ['us_id' => 'act_us_id']);
    }


}
