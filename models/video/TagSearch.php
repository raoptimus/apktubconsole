<?php

namespace app\models\video;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * DailyStatSearch represents the model behind the search form about `app\models\DailyStat`.
 */
class TagSearch extends Tag
{
    public $searchTitle;

    public function init() {

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['VideoCount'], 'integer',],
            [['searchTitle'], 'safe',],
        ];
    }

    public function attributes()
    {
        return [
            '_id',
            'VideoCount',
        ];
    }

    public function formName()
    {
        return "f";
    }

    public function search($params)
    {
        $query = Tag::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'VideoCount' => SORT_DESC
                ],
                'attributes' => [
                    '_id',
                    'VideoCount',
                    'searchTitle' => [
                        'asc' => ['Title.Quote' => SORT_ASC],
                        'desc' => ['Title.Quote' => SORT_DESC],
                        'default' => SORT_DESC,
                        'label' => 'Title',
                    ]
                ],
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

        $query->andFilterWhere(['_id' => $this->_id]);
        $query->andFilterWhere(['VideoCount' => $this->VideoCount]);
        $query->andFilterWhere(['like','Title.Quote',$this->searchTitle]);

        return $dataProvider;
    }
}