<?php

namespace app\modules\user\models;

use Yii;

/**
 * This is the model class for table "{{%department}}".
 *
 * @property integer $dep_id
 * @property string $dep_name
 * @property string $dep_shortname
 * @property integer $dep_active
 */
class Department extends \yii\db\ActiveRecord
{
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
            [['dep_name', 'dep_shortname'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'dep_id' => 'Dep ID',
            'dep_name' => 'Dep Name',
            'dep_shortname' => 'Dep Shortname',
            'dep_active' => 'Dep Active',
        ];
    }
}
