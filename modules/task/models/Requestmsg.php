<?php

namespace app\modules\task\models;

use Yii;

/**
 * This is the model class for table "{{%requestmsg}}".
 *
 * @property integer $req_id
 * @property integer $req_user_id
 * @property integer $req_task_id
 * @property string $req_text
 * @property string $req_comment
 * @property string $req_created
 * @property string $req_data
 * @property integer $req_is_active
 */
class Requestmsg extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%requestmsg}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['req_user_id', 'req_task_id'], 'required'],
            [['req_user_id', 'req_task_id', 'req_is_active'], 'integer'],
            [['req_created'], 'safe'],
            [['req_data'], 'string'],
            [['req_text', 'req_comment'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'req_id' => 'ID',
            'req_user_id' => 'Пользователь',
            'req_task_id' => 'Задача',
            'req_text' => 'Текст',
            'req_comment' => 'Комментарий',
            'req_created' => 'Создан',
            'req_data' => 'Данные',
            'req_is_active' => 'Активен',
        ];
    }
}
