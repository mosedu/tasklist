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

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

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
