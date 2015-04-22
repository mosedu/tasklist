<?php

namespace app\modules\task\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\task\models\Tasklist;

/**
 * TasklistSearch represents the model behind the search form about `app\modules\task\models\Tasklist`.
 */
class TasklistSearch extends Tasklist
{
    public $datestart;
    public $datefinish;

    public $actdatestart;
    public $actdatefinish;

    public $numchangesstart;
    public $numchangesfinish;

    public $showFilterForm = 0; // показывать/скрывать форму фильтрации
    public $showFinishedTask = 0; // показывать/скрывать завершенные задачи
    public $showTaskSummary = 0; // показывать/скрывать поле отчет о выполнении

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['datestart', 'datefinish', 'actdatestart', 'actdatefinish'], 'filter', 'filter' => function($val) { if( $val && preg_match('|^(\\d{2})\\.(\\d{2})\\.(\\d{4})$|', $val, $a) ) { $val = "{$a[3]}-{$a[2]}-{$a[1]}"; } return $val; }],
            [['task_id', 'task_dep_id', 'task_type', 'task_numchanges', ], 'integer'], // 'task_num',
            [['task_num', ], 'match', 'pattern' => '|^\\d+[\\d.]*$|', 'message'=>'Нужно циферки ввести в формате 1.3 или 2', ],
            [['numchangesstart', 'numchangesfinish', ], 'integer'],
            [['task_progress'], 'in', 'range'=>array_keys(Tasklist::getAllProgresses()), 'allowArray' => true, ],
            [['datestart', 'datefinish', 'task_direct', 'task_name', 'task_createtime', 'task_finaltime', 'task_actualtime', 'task_reasonchanges', 'task_summary'], 'safe'],
            [['task_dep_id'], 'filter', 'filter' => function($val){ return ( $val <=0 ) ? null : $val; }, ],
            [['showFilterForm', 'showFinishedTask', 'showTaskSummary'], 'integer', ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            [
                'datestart' => 'Базовый срок от',
                'datefinish' => 'до',
                'actdatestart' => 'Актуальный срок от',
                'actdatefinish' => 'до',
                'showFilterForm' => 'Форма',
                'showFinishedTask' => 'Завершенные',
                'showTaskSummary' => 'Отчет',
                'numchangesstart' => 'Кол-во изм. от',
                'numchangesfinish' => 'до',
            ]
        );

    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Tasklist::find();
        $query->with(['changes', 'department']);
        $query->joinWith(['department']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        $this->setCookie($params);

        $dataProvider->sort->attributes['task_num'] = [
            'asc' => ['dep_num' => SORT_ASC, 'task_num' => SORT_ASC],
            'desc' => ['dep_num' => SORT_DESC, 'task_num' => SORT_DESC],
        ];

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }


        $a = explode('.', $this->task_num);
        $tasknum = '';
        if( count($a) > 1 ) {
            $tasknum = $a[1];
        }

        if( !Yii::$app->user->can('createUser') ) {
            $this->task_dep_id = Yii::$app->user->getIdentity()->us_dep_id;
            $tasknum = ( count($a) > 1 ) ? $a[1] : $a[0];
        }
        else {
            if( !empty($this->task_num) ) {
                $this->task_dep_id = $a[0];
                $tasknum = (count($a) > 1) ? $a[1] : '';
            }
        }

        if( $this->numchangesstart > 0 ) {
            $query->andFilterWhere(['>=', 'task_numchanges', $this->numchangesstart]);
        }

        if( $this->numchangesfinish > 0 ) {
            $query->andFilterWhere(['<', 'task_numchanges', $this->numchangesfinish]);
        }

        if( $this->datestart ) {
            $query->andFilterWhere(['>=', 'task_finaltime', $this->datestart]);
        }

        if( $this->datefinish ) {
            $query->andFilterWhere(['<', 'task_finaltime', $this->datefinish]);
        }

        if( $this->actdatestart ) {
            $query->andFilterWhere(['>=', 'task_actualtime', $this->actdatestart]);
        }

        if( $this->actdatefinish ) {
            $query->andFilterWhere(['<', 'task_actualtime', $this->actdatefinish]);
        }

        if( !$this->showFinishedTask && empty($this->task_progress) ) {
            $query->andFilterWhere(['<>', 'task_progress', Tasklist::PROGRESS_FINISH]);
        }

        $query->andFilterWhere([
            'task_id' => $this->task_id,
            'task_dep_id' => $this->task_dep_id,
            'task_num' => $tasknum,
            'task_type' => $this->task_type,
            'task_active' => Tasklist::STATUS_ACTIVE,
//            'task_createtime' => $this->task_createtime,
//            'task_finaltime' => $this->task_finaltime,
//            'task_actualtime' => $this->task_actualtime,
            'task_numchanges' => $this->task_numchanges,
            'task_progress' => $this->task_progress,
        ]);

        $query->andFilterWhere(['like', 'task_direct', $this->task_direct])
            ->andFilterWhere(['like', 'task_name', $this->task_name])
            ->andFilterWhere(['like', 'task_reasonchanges', $this->task_reasonchanges])
            ->andFilterWhere(['like', 'task_summary', $this->task_summary]);

        return $dataProvider;
    }

    /**
     * Установка значений флажков из входных данных в куки или по кукам, если не было входных данных
     *
     * @param array $params
     */
    public function setCookie($params) {
        $aNames = ['showFilterForm', 'showFinishedTask', 'showTaskSummary'];
        $sFormName = $this->formName();
        $aCookies = Yii::$app->request->cookies;
        if( isset($params[$sFormName]) ) {
            $data = $params[$sFormName];
            foreach($aNames As $v) {
                if( isset($data[$v]) ) {
                    Yii::$app->response->cookies->add(new \yii\web\Cookie([
                        'name' => $v,
                        'value' => $data[$v],
                    ]));
                }
                else {
                    if( $aCookies->has($v) ) {
                        $this->$v = $aCookies->getValue($v);
                    }
                }
            }
        }
        else {
            foreach ($aNames As $v) {
                if( $aCookies->has($v) ) {
                    $this->$v = $aCookies->getValue($v);
                }
            }
        }

    }

    /**
     * Получаем массив аттрибутов для создания ссылки
     *
     * @return array
     */
    public function getSearchParams() {
        $aAttr = $this->safeAttributes();
        $aRet = [];
        $sFormName = $this->formName();

        foreach($aAttr As $v) {
            if( empty($this->$v) ) {
                continue;
            }
            $aRet[$sFormName . '['.$v.']'] = $this->$v;
        }

        return $aRet;
    }

}
