<?php

namespace app\modules\task\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\base\Event;
use app\modules\task\models\Tasklist;
use app\modules\user\models\User;
use yii\db\Expression;
use app\components\ExecfunctionBehavior;

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
    public $task_create_date;

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

            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'req_created',
                ],
                'value' => function ($event) {
                    /** @var Event $event */
                    /** @var Requestmsg $ob */
                    return new Expression('NOW()');
                },
            ],

            [
                'class' => ExecfunctionBehavior::className(),
                'function_events' => [
                    ActiveRecord::EVENT_AFTER_INSERT,
                ],
                'function_def' => function ($model, $event) {
                    /** @var Event $event */
                    /** @var Requestmsg $model */
                    $sTemplate = 'request_task_finish_date';
                    $sTitle = 'Запрос на перенос срока окончания задачи';
                    if( $model->task->task_dep_id != 1 ) {
                        $aUsers = User::find()
                            ->where([
                                'us_dep_id' => 1,
                                'us_active' => User::STATUS_ACTIVE,
                            ])
                            ->all();
                        foreach($aUsers As $ob) {
                            $ob->sendNotificate(
                                $sTemplate,
                                $sTitle,
                                [
                                    'task' => $model->task,
                                    'request' => $model,
                                    'user' => $model->user,
                                ]
                            );
                        }
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
        return '{{%requestmsg}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['req_user_id', 'req_task_id', 'req_text', ], 'required'],
            [
                ['new_finish_date', ],
                'required',
                'when' => function($model){ return $model->isNewRecord; },
                'whenClient' => 'function (attribute, value) { return '.($this->isNewRecord ? 'true' : 'false').'; }',
                'message' => 'Необходимо выбрать новую дату окончания',
            ],
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
        return $this->hasOne(Tasklist::className(), ['task_id' => 'req_task_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['us_id' => 'req_user_id']);
    }

    public function prepareData()
    {
        $a = explode('.', $this->new_finish_date);
        return $this->req_data = serialize([
            'task_finishtime' => date('Y-m-d H:i:s', mktime(23, 59, 59, $a[1], $a[0], $a[2])),
        ]);
    }

}
