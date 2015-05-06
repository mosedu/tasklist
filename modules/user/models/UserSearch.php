<?php

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;

/**
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class UserSearch extends User
{
    public $fname = ''; // полное имя

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['us_id', 'us_active', 'us_dep_id'], 'integer'],
            [['fname', 'us_role_name', 'us_email', 'us_password_hash', 'us_name', 'us_secondname', 'us_lastname', 'us_login', 'us_logintime', 'us_createtime', 'us_workposition', 'us_auth_key', 'us_email_confirm_token', 'us_password_reset_token'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(
            parent::attributeLabels(),
            ['fname' => 'ФИО', ]
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
        $query = User::find();
        $query->with('department');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if( !Yii::$app->user->can('admin') ) {
            $this->us_active = User::STATUS_ACTIVE;
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'us_id' => $this->us_id,
            'us_active' => $this->us_active,
            'us_dep_id' => $this->us_dep_id,
//            'us_logintime' => $this->us_logintime,
//            'us_createtime' => $this->us_createtime,
        ]);

        $query->andFilterWhere(['like', 'us_email', $this->us_email])
            ->andFilterWhere(['like', 'us_role_name', $this->us_role_name])
            ->andFilterWhere(['in', 'us_role_name', array_keys(User::getUserRoles())]);
        if( !empty($this->fname) ) {
            $query->andFilterWhere([
                'or',
                ['like', 'us_name', $this->fname],
                ['like', 'us_secondname', $this->fname],
                ['like', 'us_lastname', $this->fname]
            ]);
        }
//            ->andFilterWhere(['like', 'us_password_hash', $this->us_password_hash])
//            ->andFilterWhere(['like', 'us_name', $this->us_name])
//            ->andFilterWhere(['like', 'us_secondname', $this->us_secondname])
//            ->andFilterWhere(['like', 'us_lastname', $this->us_lastname])
//            ->andFilterWhere(['like', 'us_login', $this->us_login])
//            ->andFilterWhere(['like', 'us_workposition', $this->us_workposition])
//            ->andFilterWhere(['like', 'us_auth_key', $this->us_auth_key])
//            ->andFilterWhere(['like', 'us_email_confirm_token', $this->us_email_confirm_token])
//            ->andFilterWhere(['like', 'us_password_reset_token', $this->us_password_reset_token]);

        return $dataProvider;
    }

    public function searchWorker($params) {
        $query = User::find();
        $query->with('department');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);
        if( !Yii::$app->user->can('admin') ) {
            $this->us_active = User::STATUS_ACTIVE;
            $this->us_dep_id = Yii::$app->user->identity->us_dep_id;
        }

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'us_id' => $this->us_id,
            'us_active' => $this->us_active,
            'us_dep_id' => $this->us_dep_id,
//            'us_logintime' => $this->us_logintime,
//            'us_createtime' => $this->us_createtime,
        ]);

        $query->andFilterWhere(['like', 'us_email', $this->us_email])
            ->andFilterWhere(['us_role_name' => User::ROLE_WORKER]);
        if( !empty($this->fname) ) {
            $query->andFilterWhere([
                'or',
                ['like', 'us_name', $this->fname],
                ['like', 'us_secondname', $this->fname],
                ['like', 'us_lastname', $this->fname]
            ]);
        }

        return $dataProvider;
    }
}
