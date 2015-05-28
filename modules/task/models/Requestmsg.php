<?php

namespace app\modules\task\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\base\Event;
use app\modules\task\models\Tasklist;

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
    public $new_finish_date;

    public function behaviors() {
        return [
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_VALIDATE => 'req_user_id',
                ],
                'value' => function ($event) {
                    /** @var Event $event */
                    /** @var Requestmsg $ob */
                    $ob = $event->sender;
                    $val = $ob->req_user_id;
                    if( $ob->isNewRecord ) {
                        $val = Yii::$app->user->identity->getId();
                    }
                    return $val;
                },
            ],

        ];
    }
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
            [['req_created', 'new_finish_date', ], 'safe'],
            [['req_data'], 'string'],
            [['req_text', ], 'string', 'max' => 255 ],
            [['req_comment', ], 'string', ],
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
            'req_text' => 'Причина переноса',
            'req_comment' => 'Комментарий',
            'req_created' => 'Создан',
            'req_data' => 'Данные',
            'req_is_active' => 'Активен',
            'new_finish_date' => 'Новая дата окончания',
        ];
    }

    public function getTask()
    {
        // Order has_one Customer via Customer.id -> customer_id
        return $this->hasOne(Tasklist::className(), ['task_id' => 'req_task_id']);
    }
}
