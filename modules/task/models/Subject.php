<?php

namespace app\modules\task\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\BaseActiveRecord;

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
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    BaseActiveRecord::EVENT_BEFORE_INSERT => ['subj_created'],
                ],
//                'createdAtAttribute' => 'subj_created',
                'value' => new Expression('NOW()'),
            ],

        ];
    }

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

    public static function import($data) {
        foreach($data As $k=>$v) {
            $ob = self::find()->where(['subj_title' => $v])->one();
            if( $ob === null ) {
                Yii::info('Subject::import(): create ['.$k.'] ' . $v);
                $ob = new Subject();
                $ob->subj_title = $v;
                $ob->subj_is_active = 1;
                if( !$ob->save() ) {
                    Yii::info('Error save import data to Subject: ' . print_r($ob->getErrors(), true) . "\nattributes = " . print_r($ob->attributes, true));
                }
            }
            else {
                Yii::info('Subject::import(): exists ['.$k.'] ' . $v . ' = ' . print_r($ob->attributes, true));
            }
        }
    }
}
