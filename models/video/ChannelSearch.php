<?php

namespace app\models\video;

use Yii;
use yii\data\ActiveDataProvider;

class ChannelSearch extends Channel
{
    public function init() {

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Title'],'string'],
            [['_id'],'integer']
        ];
    }

    public function search($params)
    {
        $query = Channel::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    '_id',
                    'Title'
                ],
                'defaultOrder' => [
                    '_id'=> SORT_ASC
                ]
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['_id' => $this->_id]);
        $query->andFilterWhere(['like','Title', $this->Title]);

        return $dataProvider;
    }
}