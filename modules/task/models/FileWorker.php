<?php

namespace app\modules\task\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\task\models\Worker;

/**
 * FileWorker represents the model behind the search form about `app\modules\task\models\Worker`.
 */
class FileWorker extends Worker
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['worker_id', 'worker_task_id', 'worker_us_id'], 'integer'],
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
        $query = Worker::find();

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
            'worker_id' => $this->worker_id,
            'worker_task_id' => $this->worker_task_id,
            'worker_us_id' => $this->worker_us_id,
        ]);

        return $dataProvider;
    }
}
