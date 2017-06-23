<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

class JournalSearch extends Journal
{
    public $AddedDateFrom;
    public $AddedDateTo;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [["_id", "UserId", "UserIp", "Operation", "ObjectId", "ObjectName", "Details"], 'string'],
            [["AddedDate"], 'integer'],
            [["AddedDateFrom","AddedDateTo"], 'safe']
        ];
    }

    public function init() {

    }

    public function formName()
    {
        return "j";
    }

    public function search($params)
    {
        $query = Journal::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    '_id'=> SORT_DESC
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

        $query->andFilterWhere(['_id' => $this->_id])
            ->andFilterWhere(['UserId' => User::getIdByName($this->UserId)])
            ->andFilterWhere(['like', 'UserIp', $this->UserIp])
            ->andFilterWhere(['like', 'Operation', $this->Operation])
            ->andFilterWhere(['ObjectId' => $this->ObjectId])
            ->andFilterWhere(['like', 'Details', $this->Details])
            ->andFilterWhere(['like', 'ObjectName', $this->ObjectName])
            ->andFilterWhere(['between', 'AddedDate',$this->AddedDateFrom,$this->AddedDateTo]);


        return $dataProvider;
    }}