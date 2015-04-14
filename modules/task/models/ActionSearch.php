<?php

namespace app\modules\task\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\task\models\Action;

/**
 * ActionSearch represents the model behind the search form about `app\modules\task\models\Action`.
 */
class ActionSearch extends Action
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['act_id', 'act_us_id', 'act_type', 'act_table_pk'], 'integer'],
            [['act_createtime', 'act_data', 'act_table'], 'safe'],
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
        $query = Action::find();

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
            'act_id' => $this->act_id,
            'act_us_id' => $this->act_us_id,
            'act_type' => $this->act_type,
            'act_createtime' => $this->act_createtime,
            'act_table_pk' => $this->act_table_pk,
        ]);

        $query->andFilterWhere(['like', 'act_data', $this->act_data])
            ->andFilterWhere(['like', 'act_table', $this->act_table]);

        return $dataProvider;
    }
}
