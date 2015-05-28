<?php

namespace app\modules\task\models;

use Yii;

/**
 * This is the model class for table "{{%subject}}".
 *
 * @property integer $subj_id
 * @property string $subj_title
 * @property string $subj_created
 * @property integer $subj_dep_id
 * @property string $subj_comment
 * @property integer $subj_is_active
 */
class Subject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%subject}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subj_title'], 'required'],
            [['subj_title'], 'string'],
            [['subj_created'], 'safe'],
            [['subj_dep_id', 'subj_is_active'], 'integer'],
            [['subj_comment'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'subj_id' => 'ID',
            'subj_title' => 'Название',
            'subj_created' => 'Создано',
            'subj_dep_id' => 'Отдел',
            'subj_comment' => 'Комментарий',
            'subj_is_active' => 'Активно',
        ];
    }
}
