<?php

namespace app\modules\task\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\task\models\Subject;

/**
 * SubjectSearch represents the model behind the search form about `app\modules\task\models\Subject`.
 */
class SubjectSearch extends Subject
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['subj_id', 'subj_dep_id', 'subj_is_active'], 'integer'],
            [['subj_title', 'subj_created', 'subj_comment'], 'safe'],
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
        $query = Subject::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if( $this->subj_is_active == '' ) {
            $this->subj_is_active = 1;
        }

        $query->andFilterWhere([
            'subj_id' => $this->subj_id,
            'subj_created' => $this->subj_created,
            'subj_dep_id' => $this->subj_dep_id,
            'subj_is_active' => $this->subj_is_active,
        ]);

        $query->andFilterWhere(['like', 'subj_title', $this->subj_title])
            ->andFilterWhere(['like', 'subj_comment', $this->subj_comment]);

        return $dataProvider;
    }
}
