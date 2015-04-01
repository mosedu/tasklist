<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\Department;

/**
 * DepartmentSearch represents the model behind the search form about `app\modules\user\models\Department`.
 */
class DepartmentSearch extends Department
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dep_id', 'dep_active'], 'integer'],
            [['dep_name', 'dep_shortname'], 'safe'],
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
        $query = Department::find();

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
            'dep_id' => $this->dep_id,
            'dep_active' => $this->dep_active,
        ]);

        $query->andFilterWhere(['like', 'dep_name', $this->dep_name])
            ->andFilterWhere(['like', 'dep_shortname', $this->dep_shortname]);

        return $dataProvider;
    }
}
