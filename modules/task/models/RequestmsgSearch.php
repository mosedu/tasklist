<?php

namespace app\modules\task\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\task\models\Requestmsg;

/**
 * RequestmsgSearch represents the model behind the search form about `app\modules\task\models\Requestmsg`.
 */
class RequestmsgSearch extends Requestmsg
{
    public function behaviors() {
        return [
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['req_id', 'req_user_id', 'req_task_id', 'req_is_active'], 'integer'],
            [['req_text', 'req_comment', 'req_created', 'req_data'], 'safe'],
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
        $query = Requestmsg::find();
        $query->with(['task', 'user', ]);
        $query->joinWith(['task', 'user', ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        $dataProvider->sort->defaultOrder = [
            'req_is_active' => SORT_DESC,
            'req_created' => SORT_DESC,
        ];

        $dataProvider->sort->attributes['req_user_id'] = [
            'asc' => ['us_lastname' => SORT_ASC, 'us_name' => SORT_ASC],
            'desc' => ['us_lastname' => SORT_DESC, 'us_name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['req_task_id'] = [
            'asc' => ['task_dep_id' => SORT_ASC, 'task_num' => SORT_ASC],
            'desc' => ['task_dep_id' => SORT_DESC, 'task_num' => SORT_DESC],
        ];

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'req_id' => $this->req_id,
            'req_user_id' => $this->req_user_id,
            'req_task_id' => $this->req_task_id,
            'req_created' => $this->req_created,
            'req_is_active' => $this->req_is_active,
        ]);

        $query->andFilterWhere(['like', 'req_text', $this->req_text])
            ->andFilterWhere(['like', 'req_comment', $this->req_comment])
            ->andFilterWhere(['like', 'req_data', $this->req_data]);

        return $dataProvider;
    }
}
