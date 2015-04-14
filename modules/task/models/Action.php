<?php

namespace app\modules\task\models;

use Yii;

/**
 * This is the model class for table "{{%action}}".
 *
 * @property integer $act_id
 * @property integer $act_us_id
 * @property integer $act_type
 * @property string $act_createtime
 * @property string $act_data
 */
class Action extends \yii\db\ActiveRecord
{
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
            [['act_us_id', 'act_type'], 'integer'],
            [['act_createtime'], 'required'],
            [['act_createtime'], 'safe'],
            [['act_data'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'act_id' => 'Act ID',
            'act_us_id' => 'Act Us ID',
            'act_type' => 'Act Type',
            'act_createtime' => 'Act Createtime',
            'act_data' => 'Act Data',
        ];
    }
}
