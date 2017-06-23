<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * @var \MongoId _id
 * @var string Title
 * @var string Name
 * @var \MongoGridFSFile Icon
 * @var string Desc
 * @var string Age
 * @var string Rating
 * @var string Note
 * @var string Countries
 * @var array Screenshots
 */
class AdsSearch extends Ads
{
    public function rules()
    {
        return [
            [['_id','Title','Name','Age','Rating','Countries','Note','Status'],'safe']
        ];
    }

    public function formName()
    {
        return "ads";
    }

    public function init()
    {
    }

    public function search($params)
    {
        $query = Ads::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'attributes' => $this->attributes(),
                'defaultOrder' => [
                    'Sort'=> SORT_DESC
                ]
            ],
/*            'pagination' => [
                'pageSize' => Yii::$app->params['appUserPageSize'],
            ],*/
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['_id' => $this->_id])
            ->andFilterWhere(['like', 'Title.Quote', $this->Title])
            ->andFilterWhere(['like', 'Name.Quote', $this->Name])
            ->andFilterWhere(['like', 'Note', $this->Note])
            ->andFilterWhere(['Countries' => $this->Countries]);

        if (!empty($this->Status) && $this->Status != 'All') {
            $query->andFilterWhere(['Status' => $this->Status]);
        }

        if (!empty($this->Age)) {
            $query->andFilterWhere(['Age' => intval($this->Age)]);
        }

        if (!empty($this->Rating)) {
            $query->andFilterWhere(['Rating' => floatval($this->Rating)]);
        }

        return $dataProvider;
    }
}