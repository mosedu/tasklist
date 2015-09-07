<?php

namespace app\modules\cron\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\cron\models\Crontab;

/**
 * CrontabSearch represents the model behind the search form about `app\modules\cron\models\Crontab`.
 */
class CrontabSearch extends Crontab
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cron_id', 'cron_isactive'], 'integer'],
            [['cron_min', 'cron_hour', 'cron_day', 'cron_wday', 'cron_path', 'cron_tstart', 'cron_tlast', 'cron_comment'], 'safe'],
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
        $query = Crontab::find();

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
            'cron_id' => $this->cron_id,
            'cron_tstart' => $this->cron_tstart,
            'cron_tlast' => $this->cron_tlast,
            'cron_isactive' => $this->cron_isactive,
        ]);

        $query->andFilterWhere(['like', 'cron_min', $this->cron_min])
            ->andFilterWhere(['like', 'cron_hour', $this->cron_hour])
            ->andFilterWhere(['like', 'cron_day', $this->cron_day])
            ->andFilterWhere(['like', 'cron_wday', $this->cron_wday])
            ->andFilterWhere(['like', 'cron_path', $this->cron_path])
            ->andFilterWhere(['like', 'cron_comment', $this->cron_comment]);

        return $dataProvider;
    }
}
