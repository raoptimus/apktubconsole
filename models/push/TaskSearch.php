<?php

namespace app\models\push;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * DailyStatSearch represents the model behind the search form about `app\models\DailyStat`.
 */
class TaskSearch extends Task
{
    public function init() {
        $this->Enabled = 'all';
        $this->Deleted = 'all';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                ['Deleted', 'Enabled'],
                'in',
                'range' => [0, 1, 'all'],
            ],
            [
                'Note',
                'string',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'Deleted',
            'Enabled',
            'Note'
        ];
    }


    public function attributeLabels()
    {
        return [
            'Enabled' => Yii::t('dict', 'Enabled'),
        ];
    }

    public function formName()
    {
        return "f";
    }

    public function search($params)
    {
        $query = Task::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
//                'attributes' => $this->attributes(),
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

        if ($this->Enabled !== 'all' || $this->Deleted !== 'all') {
            if (boolval($this->Deleted)) {
                $query->andFilterWhere(['Deleted' => boolval($this->Deleted)]);
            } else {
                $query->andWhere(['Enabled' => boolval($this->Enabled)]);
                $query->andFilterWhere(['or',['Deleted' => boolval($this->Deleted)],['Deleted' => ['$exists' => false]]]);
            }
        }

        $query->andFilterWhere(['like','Note', $this->Note]);


        return $dataProvider;
    }
}