<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ApplicationSearch represents the model behind the search form about `app\models\Application`.
 */
class ApplicationSearch extends Application
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
//            [['BuildVer', 'Status'], 'integer'],
//            [['_id', 'Name', 'Ver', 'Description'], 'safe'],
            [['Status'],'safe']
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
        $query = Application::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    'AddedDate',
                    'ReleaseDate',
                    'BuildVer',
                    'Ver' => [
                        'asc' => ['Ver' => SORT_ASC, 'BuildVer' => SORT_ASC],
                        'desc' => ['Ver' => SORT_DESC, 'BuildVer' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Ver',
                    ]
                ],
                'defaultOrder' => ['Ver' => SORT_DESC]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            '_id' => $this->_id,
            'BuildVer' => $this->BuildVer,
            'Status' => $this->Status,
        ]);

        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Ver', $this->Ver])
            ->andFilterWhere(['like', 'Description', $this->Description]);

        return $dataProvider;
    }
}
