<?php

namespace app\models\video;

use Yii;
use yii\data\ActiveDataProvider;

class ActorSearch extends Actor
{
    public function init() {

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Name'],'string'],
            [['_id'],'integer']
        ];
    }

    public function search($params)
    {
        $query = Actor::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => [
                    '_id',
                    'Name'
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
        $query->andFilterWhere(['like','Name', $this->Name]);

        return $dataProvider;
    }
}