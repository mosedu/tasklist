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
            [['dep_id', 'dep_active', 'dep_num'], 'integer'],
            [['dep_name', 'dep_shortname'], 'safe'],
            [['dep_user_roles'], 'string', 'max' => 255],
            [['dep_user_roles'], 'in', 'range' => array_keys(User::getUserRoles())],
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
            'sort'=> [
                'defaultOrder' => [
                    'dep_num' => SORT_ASC
                ],
            ],

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
            'dep_num' => $this->dep_num,
            'dep_user_roles' => $this->dep_user_roles,
        ]);

        $query->andFilterWhere(['like', 'dep_name', $this->dep_name])
            ->andFilterWhere(['like', 'dep_shortname', $this->dep_shortname]);

        return $dataProvider;
    }
}
