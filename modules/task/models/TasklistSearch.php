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
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['task_id', 'task_dep_id', 'task_num', 'task_type', 'task_timechanges', 'task_progress'], 'integer'],
            [['task_direct', 'task_name', 'task_createtime', 'task_finaltime', 'task_actualtime', 'task_reasonchanges', 'task_summary'], 'safe'],
        ];
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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'task_id' => $this->task_id,
            'task_dep_id' => $this->task_dep_id,
            'task_num' => $this->task_num,
            'task_type' => $this->task_type,
            'task_createtime' => $this->task_createtime,
            'task_finaltime' => $this->task_finaltime,
            'task_actualtime' => $this->task_actualtime,
            'task_timechanges' => $this->task_timechanges,
            'task_progress' => $this->task_progress,
        ]);

        $query->andFilterWhere(['like', 'task_direct', $this->task_direct])
            ->andFilterWhere(['like', 'task_name', $this->task_name])
            ->andFilterWhere(['like', 'task_reasonchanges', $this->task_reasonchanges])
            ->andFilterWhere(['like', 'task_summary', $this->task_summary]);

        return $dataProvider;
    }
}
