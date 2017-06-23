<?php

namespace app\models\premium;

use Yii;
use yii\data\ActiveDataProvider;

class TariffSearch extends Tariff
{
    public $Currency = 'all';
    public $Enabled = 'all';
    public $Price;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["DisplayPrice", "AproxPrice", "Price", "Currency", "Title", 'Enabled', "Time"], 'string'],
        ];
    }

    public function init()
    {

    }

    public function formName()
    {
        return "t";
    }

    public function search($params)
    {
        $query = Tariff::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    '_id' => SORT_DESC
                ],
                'attributes' => [
                    '_id',
                    'Currency',
                    'Time',
                    'Enabled',
                    'CreationDate',
                    'DisplayPrice' => [
                        'asc' => ['AproxPrice' => SORT_ASC],
                        'desc' => ['AproxPrice' => SORT_DESC],
                    ],
                    'Title' => [
                        'asc' => ['Title.Quote' => SORT_ASC],
                        'desc' => ['Title.Quote' => SORT_DESC],
                    ]
                ]
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['appUserPageSize'],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['like', 'Title.Quote', $this->Title])
            ->andFilterWhere(['DisplayPrice' => $this->DisplayPrice])
            ->andFilterWhere(['Time' => $this->Time]);

        if ($this->Currency != 'all') {
            $query->andFilterWhere(['Currency' => $this->Currency]);
        }
        if ($this->Enabled != 'all') {
            $query->andFilterWhere(['Enabled' => boolval($this->Enabled)]);
        }
        if (!empty($this->AproxPrice)) {
            $query->andFilterWhere(['AproxPrice' => floatval($this->AproxPrice)]);
        }

        return $dataProvider;
    }
}